<?php

namespace App\Http\Controllers\Gen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Auth;
use Config;
use Lang;
use Log;

use App\Entry;
use App\Gen\Book;
use App\Gen\History;
use App\Gen\Spanish;
use App\Site;
use App\Status;
use App\Tag;
use App\User;

define('PREFIX', '/books/');
define('VIEW', '/books/view/');
define('VIEWS', 'gen.books');
define('LOG_CLASS', 'BookController');

class BookController extends Controller
{
	private $redirectTo = null;

	public function __construct()
	{
    	$this->redirectTo = route('books', ['locale' => app()->getLocale()]);

        $this->middleware('admin')->except([
            'index', 'view', 'permalink',
            'read', 'readBook', 'chapters',
            'stats',
        ]);

		parent::__construct();
	}

    public function admin(Request $request, $locale)
    {
		$records = [];

		try
		{
			$records = Entry::select()
				->where('type_flag', ENTRY_TYPE_BOOK)
				->orderBy('id', 'DESC')
				->get();
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error getting record list'));
		}

		return view(VIEWS . '.index', [
			'records' => $records,
		]);
    }

    public function index(Request $request, $locale)
    {
		//todo: $this->saveVisitor(LOG_MODEL_BOOKS, LOG_PAGE_INDEX);

		$records = Entry::getRecentList(['type' => ENTRY_TYPE_BOOK, 'id' => getLanguageId(), 'limit' => 5])['records'];
		$books = Entry::getBookTags();

    	return view(VIEWS . '.index', [
			'books' => $books,
			'records' => $records,
			'page_title' => 'Books',
			'index' => 'books',
			'isIndex' => true,
		]);
    }

    public function chapters(Request $request, $locale, Tag $tag)
    {
    	return view(VIEWS . '.chapters', [
			'book' => $tag,
			'page_title' => 'Books',
			'index' => 'books',
			'isIndex' => true,
		]);
    }

    public function add()
    {
		return view(VIEWS . '.add', [
			'languageOptions' => getLanguageOptions(),
			'selectedOption' => getLanguageId(),
			]);
	}

    public function addChapter(Request $request, $locale, Tag $tag)
    {
        $book = $tag;

        // figure out what the next chapter number should be by looking at the last chapter
        $chapterNbr = -1; //count($book->entries);
        $count = count($book->entries);
        if ($count > 0)
        {
            $last = $book->entries[$count - 1];
            $words = explode(' ', $last->title);
            if (count($words) > 1)
                $chapterNbr = intval($words[1]);
        }

        $chapterNbr++; // increment for next chapter number to be added

        //dump($book->entries);

		return view(VIEWS . '.add', [
			'languageOptions' => getLanguageOptions(),
			'selectedOption' => getLanguageId(),
			'chapter' => $chapterNbr,
			'book' => $book,
			]);
	}

    public function create(Request $request, $locale)
    {
		$record = new Entry();

		$record->site_id            = Site::getId();
		$record->user_id            = Auth::id();
		$record->title 				= trimNull($request->title);
		$record->display_order      = intval($request->display_order);
		if (!isset($record->title))
		{
		    // if title not set, set it as "Chapter X"
		    $record->title = trans_choice('proj.Chapter', 1);

		    // display_order is chapter number
		    if ($record->display_order > 0)
		        $record->title .= ' ' . $record->display_order;
		}

		$record->description		= Str::limit($request->description, MAX_DB_TEXT_COLUMN_LENGTH);
		$record->description_short	= trimNull($request->description_short);
		$record->source				= trimNull($request->source);
		$record->source_credit		= trimNull($request->source_credit);
		$record->source_link		= trimNull($request->source_link);
		$record->display_date 		= timestamp();
		$record->release_flag 		= RELEASEFLAG_PRIVATE;
		$record->wip_flag 			= WIP_FINISHED;
		$record->language_flag		= isset($request->language_flag) ? $request->language_flag : getLanguageId();
		$record->type_flag 			= ENTRY_TYPE_BOOK;
		$record->permalink          = createPermalink($record->title, $record->created_at);

		try
		{
			if (!isset($record->title))
				throw new \Exception('Title not set');
			if (!isset($record->display_date))
				throw new \Exception('Date not set');

			$record->save();

			// set up the book tag.  has to be done after the entry is created and has an id
			$record->updateBookTag();

			$msg = 'Entry has been added';
			$status = 'success';
			if (strlen($request->description) > MAX_DB_TEXT_COLUMN_LENGTH)
			{
				$msg .= ' - DESCRIPTION TOO LONG, TRUNCATED';
				$status = 'danger';
			}

			logInfo(LOG_CLASS, __('base.New entry has been added'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error adding new record'));
			return back();
		}

		return redirect($record->getRedirect()['view']);
    }

    public function permalink(Request $request, $locale, $permalink)
    {
 		$record = null;
		$permalink = alphanum($permalink);

        $releaseFlag = Status::getReleaseFlagForUserLevel();
        $releaseFlagCondition = Status::getConditionForUserLevel();

		try
		{
			$record = Entry::select()
				->where('release_flag', $releaseFlagCondition, $releaseFlag)
				->where('permalink', $permalink)
				->first();

			if (blank($record))
			    throw new \Exception('book not found');

		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('proj.Book not found'), ['permalink' => $permalink]);
    		return redirect($this->redirectTo);
		}

        return $this->view($request, app()->getLocale(), $record);
	}

	public function view(Request $request, $locale, Entry $entry)
    {
		$record = $entry;

		$next = null;
		$prev = null;
		$options['wordCount'] = null;

		//todo: $id = isset($record) ? $record->id : null;
		//todo: $visitor = $this->saveVisitor(LOG_MODEL_ENTRIES, LOG_PAGE_PERMALINK, $id);
		//todo: $isRobot = isset($visitor) && $visitor->robot_flag;

        $backLink = route('books', ['locale' => $locale]);
		if (isset($record))
		{
			$record->tagRecent(); // tag it as recent for the user so it will move to the top of the list
			Entry::countView($record);
			$options['wordCount'] = str_word_count($record->description); // count it before <br/>'s are added
			$record->description = nl2br($record->description);
			$book = Book::getBook($entry);
			if (isset($book))
			    $backLink = route('books.chapters', ['locale' => $locale, 'tag' => $book->id]);
		}
		else
		{
			return $this->pageNotFound404($permalink);
		}

        $options['backLink'] = $backLink;
        $options['index'] = 'books';
        $options['backLinkText'] = __('ui.Back to List');
        $options['page_title'] = trans_choice('proj.Chapter', 1) . ' - ' . $record->title;
        $options['book'] = Book::getBook($record);

        $next = Book::getNextChapter($record);
        $prev = Book::getNextChapter($record, /* next = */ false);

		return view(VIEWS . '.view', [
			'options' => $options,
			'record' => $record,
			'next' => $next,
			'prev' => $prev,
			]);
    }

	public function edit(Request $request, $locale, Entry $entry)
    {
		$record = $entry;

		return view(VIEWS . '.edit', [
			'record' => $record,
			'languageOptions' => getLanguageOptions(),
			'book' => Book::getBook($record),
			]);
    }

    public function update(Request $request, $locale, Entry $entry)
    {
		$record = $entry;
		$prevTitle = $record->title;

		$record->site_id 			= Site::getId();
		$record->title 				= trimNull($request->title);
		$record->description_short	= trimNull($request->description_short);
		$record->description		= Str::limit($request->description, MAX_DB_TEXT_COLUMN_LENGTH);
		$record->source				= trimNull($request->source);
		$record->source_credit		= trimNull($request->source_credit);
		$record->source_link		= trimNull($request->source_link);
		$record->display_order 		= floatval($request->display_order);
		$record->language_flag		= isset($request->language_flag) ? $request->language_flag : Site::getLanguage()['id'];
		$record->type_flag 			= ENTRY_TYPE_BOOK;
		$record->permalink          = createPermalink($record->title, $record->created_at);

		try
		{
			$record->save();
			logInfo('update book', null, ['title' => $record->title, 'id' => $record->id, 'prevTitle' => $prevTitle, 'title' => $record->title]);

            // set up the book tag.  has to be done after the entry is created and has an id
            $record->updateBookTag();

			$msg = 'Book has been updated';
			$status = 'success';
			if (strlen($request->description) > MAX_DB_TEXT_COLUMN_LENGTH)
			{
				$msg .= ' - DESCRIPTION TOO LONG, TRUNCATED';
				$status = 'danger';
			}

			logInfo(LOG_CLASS, __('proj.' . $msg), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error updating book'));
			return back();
		}

        $url = route('books.show', ['locale' => $locale, 'permalink' => $record->permalink]);
		return redirect($url);
	}

    public function confirmDelete(Request $request, $locale, Entry $entry)
    {
		$record = $entry;

		return view(VIEWS . '.confirmdelete', [
			'record' => $record,
			'book' => Book::getBook($record),
		]);
    }

    public function delete(Request $request, $locale, Entry $entry)
    {
		$record = $entry;

		try
		{
			$record->delete();
			logInfo(LOG_CLASS, __('base.Record has been deleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error deleting record'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

    public function undelete(Request $request, $locale, $id)
    {
		$id = intval($id);

		try
		{
			$record = Entry::withTrashed()
				->where('id', $id)
				->first();

			$record->restore();
			logInfo(LOG_CLASS, __('base.Record has been undeleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error undeleting record'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

    public function deleted()
    {
		$records = []; // make this countable so view will always work

		try
		{
			$records = Entry::withTrashed()
				->where('type_flag', ENTRY_TYPE_BOOK)
				->whereNotNull('deleted_at')
				->orderBy('id', 'DESC')
				->get();
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error getting deleted records'));
		}

		return view(VIEWS . '.deleted', [
			'records' => $records,
		]);
    }

    public function publish(Request $request, $locale, Entry $entry)
    {
		$record = $entry;

		return view(VIEWS . '.publish', [
			'record' => $record,
			'release_flags' => Status::getReleaseFlags(),
			'wip_flags' => Status::getWipFlags(),
			'book' => Book::getBook($record),
		]);
    }

    public function updatePublish(Request $request, $locale, Entry $entry)
    {
		$record = $entry;

        if ($request->isMethod('get'))
        {
            // quick publish, set to toggle public / private
            $record->wip_flag = $record->isFinished() ? getConstant('wip_flag.dev') : getConstant('wip_flag.finished');
            $record->release_flag = $record->isPublic() ? RELEASEFLAG_PRIVATE : RELEASEFLAG_PUBLIC;
        }
        else
        {
            $record->wip_flag = $request->wip_flag;
            $record->release_flag = $request->release_flag;
        }

		try
		{
			$record->save();
			logInfo(LOG_CLASS, __('base.Record status has been updated'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error updating record status'), ['record_id' => $record->id]);
			return back();
		}

		return redirect(route('books.show', ['locale' => $locale, 'permalink' => $record->permalink]));
    }

    // this is read book
    public function readBook(Request $request, $locale, Tag $tag)
    {
		$sentences = [];
        $languageFlag = null;
        $recordId = null;
        $readLocation = null;

        // get all lines from all chapters
        foreach($tag->books as $chapter)
        {
            if (!isset($languageFlag))
                $languageFlag = $chapter->language_flag;

            if (!isset($recordId))
                $recordId = $chapter->id;

            if (!isset($readLocation))
                $readLocation = $chapter->tagRecent();

    		$sentences = array_merge($sentences, $chapter->getSentences());
        }

        // translations isn't handled for books yet so $lines['translation'] isn't set
        $lines['text'] = $sentences;

		$backLink = route('books.chapters', ['locale' => $locale, 'tag' => $tag->id]);

		return $this->doRead($lines, $tag->name, $recordId, $readLocation, $languageFlag, $backLink);
    }

    // this is read chapter
    public function read(Request $request, $locale, Entry $entry)
    {
        $record = $entry;
		$readLocation = $record->tagRecent(); // tag it as recent for the user so it will move to the top of the list
		Entry::countView($record);

        $title = $record->title;
		$book = Book::getBook($entry);
		if (isset($book))
		{
		    $title = $book->name . ' - ' . $record->title;
		}

		$lines = self::getLines($record);

		$backLink = route('books.show', ['locale' => $locale, 'permalink' => $record->permalink]);

		return $this->doRead($lines, $title, $record->id, $readLocation, $record->language_flag, $backLink);
    }


    public function doRead($lines, $title, $recordId, $readLocation, $language, $backLink)
    {
        $options = ['return' => $backLink];

        $labels = [
            'start' => Lang::get('proj.Start Reading'),
            'startBeginning' => Lang::get('proj.Start reading from the beginning'),
            'continue' => Lang::get('proj.Continue reading from line'),
            'locationDifferent' => Lang::get('proj.location form a different session'),
            'line' => Lang::choice('ui.Line', 1),
            'of' => Lang::get('ui.of'),
            'readingTime' => Lang::get('proj.Reading Time'),
        ];
        //dump($labels);

        $history = History::getArray($title, $recordId, HISTORY_TYPE_BOOK, HISTORY_SUBTYPE_SPECIFIC, LESSON_TYPE_READER, count($lines));

    	return view('shared.reader', [
    	    'lines' => $lines,
    	    'title' => $title,
			'recordId' => $recordId,
			'options' => $options,
			'readLocation' => Auth::check() ? $readLocation : null,
			'contentType' => 'Entry',
			'languageCodes' => getSpeechLanguage($language),
			'labels' => $labels,
			'history' => $history,
		]);
    }

    public function stats(Request $request, $locale, Tag $tag)
    {
		$record = $tag;

		$words = [];
		$i = 0;
		$wordCount = 0;
		$articleCount = 0;
		$stats = null;

		foreach($record->books as $record)
		{
			if ($record->language_flag == LANGUAGE_ES)
			{
				$stats = Spanish::getWordStats($record->description, $words);
				$wordCount += $stats['wordCount'];
				$words = $stats['sortAlpha'];

				//dump($stats);

				$articleCount++;
				//if ($articleCount > 1) break;
			}
		}

		// count possible verbs
		$possibleVerbs = 0;
		foreach($stats['sortCount'] as $key => $value)
		{
			if (Str::endsWith($key, ['ar', 'er', 'ir']))
				$possibleVerbs++;
		}

		$stats['wordCount'] = $wordCount;

    	return view('gen.books.stats', [
			'record' => $tag,
			'stats' => $stats,
			'possibleVerbs' => $possibleVerbs,
			'index' => 'books',
		]);
    }
}
