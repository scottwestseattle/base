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
	private $redirectTo = PREFIX;

	public function __construct()
	{
        $this->middleware('admin')->except([
            'index', 'view', 'permalink',
            'read', 'readBook',
        ]);

		parent::__construct();
	}

    public function admin(Request $request)
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

    public function index(Request $request)
    {
		//todo: $this->saveVisitor(LOG_MODEL_BOOKS, LOG_PAGE_INDEX);

		$records = Entry::getRecentList(['type' => ENTRY_TYPE_BOOK, 'id' => getLanguageId()], 5);
		$books = Entry::getBookTags();

    	return view(VIEWS . '.index', [
			'books' => $books,
			'records' => $records,
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

    public function addChapter(Tag $tag)
    {
        $book = $tag;
        $chapter = count($book->entries) + 1;

		return view(VIEWS . '.add', [
			'languageOptions' => getLanguageOptions(),
			'selectedOption' => getLanguageId(),
			'chapter' => $chapter,
			'book' => $book,
			]);
	}

    public function create(Request $request)
    {
		$record = new Entry();

		$record->site_id             = Site::getId();
		$record->user_id             = Auth::id();
		$record->title 				= trimNull($request->title);
		$record->description		= Str::limit($request->description, MAX_DB_TEXT_COLUMN_LENGTH);
		$record->source				= trimNull($request->source);
		$record->source_credit		= trimNull($request->source_credit);
		$record->source_link		= trimNull($request->source_link);
		$record->display_date 		= timestamp();
		$record->display_order 		= $request->display_order;
		$record->release_flag 		= RELEASEFLAG_PUBLIC;
		$record->wip_flag 			= WIP_FINISHED;
		$record->language_flag		= isset($request->language_flag) ? $request->language_flag : Site::getLanguage()['id'];
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
		return redirect($this->redirectTo . '/view/' . $record->id);
    }

    public function permalink(Request $request, $permalink)
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

        return $this->view($record);
	}

	public function view(Entry $entry)
    {
		$record = $entry;

		$next = null;
		$prev = null;
		$options['wordCount'] = null;

		//todo: $id = isset($record) ? $record->id : null;
		//todo: $visitor = $this->saveVisitor(LOG_MODEL_ENTRIES, LOG_PAGE_PERMALINK, $id);
		//todo: $isRobot = isset($visitor) && $visitor->robot_flag;

		if (isset($record))
		{
			$record->tagRecent(); // tag it as recent for the user so it will move to the top of the list
			Entry::countView($record);
			$options['wordCount'] = str_word_count($record->description); // count it before <br/>'s are added
			$record->description = nl2br($record->description);
		}
		else
		{
			return $this->pageNotFound404($permalink);
		}

        $options['backLink'] = '/books';
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

	public function edit(Entry $entry)
    {
		$record = $entry;

		return view(VIEWS . '.edit', [
			'record' => $record,
			'languageOptions' => getLanguageOptions(),
			'book' => Book::getBook($record),
			]);
    }

    public function update(Request $request, Entry $entry)
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

		return redirect('/books/show/' . $record->permalink);
	}

    public function confirmDelete(Entry $entry)
    {
		$record = $entry;

		return view(VIEWS . '.confirmdelete', [
			'record' => $record,
			'book' => Book::getBook($record),
		]);
    }

    public function delete(Request $request, Entry $entry)
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

    public function undelete(Request $request, $id)
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

    public function publish(Request $request, Entry $entry)
    {
		$record = $entry;

		return view(VIEWS . '.publish', [
			'record' => $record,
			'release_flags' => Status::getReleaseFlags(),
			'wip_flags' => Status::getWipFlags(),
			'book' => Book::getBook($record),
		]);
    }

    public function updatePublish(Request $request, Entry $entry)
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

		return redirect($this->redirectTo);
    }

    // this is read book
    public function readBook(Request $request, Tag $tag)
    {
		$lines = [];
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

            $lines = self::getLines($chapter, $lines);
        }

		return $this->doRead($lines, $tag->name, $recordId, $readLocation, $languageFlag, '/books');
    }

    // this is read chapter
    public function read(Request $request, Entry $entry)
    {
        $record = $entry;
		$readLocation = $record->tagRecent(); // tag it as recent for the user so it will move to the top of the list
		Entry::countView($record);

		$lines = [];
		$lines = self::getLines($record, $lines);

		$backLink = '/books/show/' . $record->permalink;

		return $this->doRead($lines, $record->title, $record->id, $readLocation, $record->language_flag, $backLink);
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

    	return view('shared.reader', [
    	    'lines' => $lines,
    	    'title' => $title,
			'recordId' => $recordId,
			'options' => $options,
			'readLocation' => Auth::check() ? $readLocation : null,
			'contentType' => 'Entry',
			'languageCodes' => getSpeechLanguage($language),
			'labels' => $labels,
		]);
    }

    static public function getLines(Entry $record, $lines)
    {
		$lines = array_merge($lines, Spanish::getSentences($record->title));
		$lines = array_merge($lines, Spanish::getSentences($record->description_short));
		$lines = array_merge($lines, Spanish::getSentences($record->description));

		return $lines;
    }

}
