<?php

namespace App\Http\Controllers\Gen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;
use Auth;
use App\User;
use App\Gen\Course;
use App\Gen\Lesson;
use App\Site;
use App\Status;
use Config;
use Log;

define('PREFIX', 'courses');
define('VIEWS', 'gen.courses');
define('LOG_MODEL', 'courses');
define('TITLE', 'Course');
define('TITLE_LC', 'course');
define('TITLE_PLURAL', 'Courses');
define('REDIRECT', '/courses');
define('REDIRECT_ADMIN', '/courses/admin');

class CourseController extends Controller
{
	private $redirectTo = PREFIX;

	public function __construct ()
	{
        $this->middleware('admin')->except(['index', 'view', 'permalink', 'rss', 'rssReader', 'start']);

		$this->prefix = PREFIX;
		$this->title = TITLE;
		$this->titlePlural = TITLE_PLURAL;

		parent::__construct();
	}

    public function start(Request $request)
    {
		try
		{
			$record = Course::select()
				->where('deleted_flag', 0)
				->where('site_id', Site::getId())
				->where('type_flag', COURSETYPE_SPANISH)
				->where('release_flag', '>=', RELEASEFLAG_PUBLIC)
				->orderBy('display_order')
				->first();

			return $this->view($record);
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting first course';
			logException(__FUNCTION__, $e->getMessage(), $msg);
		}

		return redirect()->back();
	}

    public function index(Request $request)
    {
		$showAll = isset($showAll);

		$public = []; // make this countable so view will always work
		$private = []; // make this countable so view will always work

		try
		{
			$public = Course::getIndex(['public']);
			$private = Course::getIndex(['private']);
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting ' . TITLE_LC . ' list';
			logException(__FUNCTION__, $e->getMessage(), $msg);
		}

		return view(VIEWS . '.index', [
			'public' => $public,
			'private' => $private,
		]);
    }

    public function admin(Request $request)
    {
		$records = []; // make this countable so view will always work

		try
		{
			$records = Course::getIndex(['all']);
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting ' . TITLE_LC . ' list';
			logException(__FUNCTION__, $e->getMessage(), $msg);
		}

		return view(VIEWS . '.admin', [
			'records' => $records,
		]);
    }

    public function add()
    {
		return view(VIEWS . '.add', [
			]);
	}

    public function create(Request $request)
    {
		$record = new Course();

		$record->user_id 		= Auth::id();
		$record->site_id 		= Site::getId();
		$record->title 			= $request->title;
		$record->description	= $request->description;
		$record->permalink		= createPermalink($request->title);
		$record->release_flag	= RELEASEFLAG_DEFAULT;
		$record->wip_flag		= WIP_DEFAULT;
        $record->type_flag      = COURSETYPE_DEFAULT;

		try
		{
			$record->save();

			$flash = 'New ' . TITLE_LC . ' has been added';
			logInfo(__FUNCTION__, $flash, ['id' => $record->id]);
		}
		catch (\Exception $e)
		{
			$msg = 'Error adding new ' . TITLE_LC;
			logException(__FUNCTION__, $e->getMessage(), $msg);

			return back();
		}

		return redirect('/' . PREFIX . '/view/' . $record->id);
    }

    public function permalink(Request $request, $permalink)
    {
		$permalink = trim($permalink);

		$record = null;

		try
		{
			$record = Course::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('release_flag', '>=', RELEASEFLAG_PUBLIC)
				->where('permalink', $permalink)
				->first();
		}
		catch (\Exception $e)
		{
			$msg = 'Entry Not Found: ' . $permalink;
			logError(__FUNCTION__, $msg, ['permalink' => $permalink]);

			return back();
		}

		return view(VIEWS . '.view', [
			'record' => $record,
			]);
	}

	public function view(Course $course)
    {
        if ($course->isTimedSlides())
            return $this->startTimedSlides($course);

		$record = $course;
		$displayCount = 0;
		$chapterCount = 0;
		$records = []; // make this countable so view will always work

		try
		{
			$records = Lesson::getChapters($course->id);

			// get the lesson count.  if only one chapter, count it's sections
			$chapterCount = count($records); // count the chapters
			if ($chapterCount == 1)
				$displayCount = count($records->first()); // show the count of sections
			else
				$displayCount = $chapterCount; // show the count of chapters
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting lesson list';
			logException(__FUNCTION__, $e->getMessage(), $msg, ['id' => $course->id]);
		}

		// put some view helpers together
		$disabled = (count($records) > 0) ? '' : 'disabled';

		$firstId = (count($records) > 0) ? $records->first()[0]->id : 0; // collection index starts at 1

		return view(VIEWS . '.view-flat', [
			'record' => $record,
			'records' => $records,
			'disabled' => $disabled,
			'firstId' => $firstId,
			'displayCount' => $displayCount,
			'chapterCount' => $chapterCount,
			]);
    }

	public function startTimedSlides(Course $course)
    {
		$record = $course;
		$count = 0;
		$records = []; // make this countable so view will always work

		try
		{
			$records = Lesson::getChapters($course->id);

			// get the lesson count.  if only one chapter, count it's sections
			$count = count($records); // count the chapters
			if ($count == 1)
			{
				$count = count($records->first()); // count the sections
			}
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting timed slides';
			logException(__FUNCTION__, $e->getMessage(), $msg, ['id' => $course->id]);
		}

		// put some view helpers together
		$disabled = (count($records) > 0) ? '' : 'disabled';
		$firstId = (count($records) > 0) ? $records->first()[0]->id : 0; // collection index starts at 1

        foreach($records as $chapter)
        {
			$times = Lesson::getTimes($chapter);

            $chapter['time'] = $times['timeSeconds'];
            $chapter['totalTime'] = $times['timeTotal'];
        }

		return view(VIEWS . '.viewTimedSlides', [
			'record' => $record,
			'records' => $records,
			'disabled' => $disabled,
			'firstId' => $firstId,
			'displayCount' => $count,
			]);
    }

	public function edit(Course $course)
    {
		$record = $course;

		return view(VIEWS . '.edit', [
			'record' => $record,
			]);
    }

    public function update(Request $request, Course $course)
    {
		$record = $course;

		$isDirty = false;
		$changes = '';

		$record->title = copyDirty($record->title, $request->title, $isDirty, $changes);
		$record->description = copyDirty($record->description, $request->description, $isDirty, $changes);
		$record->display_order = copyDirty($record->display_order, $request->display_order, $isDirty, $changes);
		$record->type_flag = copyDirty($record->type_flag, $request->type_flag, $isDirty, $changes);
		$record->site_id = copyDirty($record->site_id, $request->site_id, $isDirty, $changes);

		if ($isDirty)
		{
			try
			{
				$record->save();

				$msg = TITLE . ' has been updated';
    			logInfo(__FUNCTION__, __($msg), ['id' => $course->id]);
			}
			catch (\Exception $e)
			{
				$msg = 'Error updating ' . TITLE_LC;
    			logException(__FUNCTION__, $e->getMessage(), __($msg), ['id' => $course->id]);
			}
		}
		else
		{
			logFlash('info', __FUNCTION__, __('base.No changes were made'), ['id' => $course->id]);
		}

		return redirect('/' . PREFIX . '/view/' . $record->id);
	}

    public function confirmdelete(Course $course)
    {
		$record = $course;

		return view(VIEWS . '.confirmdelete', [
			'record' => $record,
			'children' => Lesson::getIndex($record->id),
		]);
    }

    public function delete(Request $request, Course $course)
    {
		$record = $course;

		try
		{
			$record->delete();
			logInfo(__FUNCTION__, 'Course has been deleted', ['id' => $course->id]);
		}
		catch (\Exception $e)
		{
			$msg = 'Error deleting course';
			logException(__FUNCTION__, $e->getMessage(), $msg, ['id' => $course->id]);
		}

		return redirect(REDIRECT_ADMIN);
    }

    public function publish(Request $request, Course $course)
    {
		$record = $course;

		return view(VIEWS . '.publish', [
			'record' => $record,
			'release_flags' => Status::getReleaseFlags(),
			'wip_flags' => Status::getWipFlags(),
		]);
    }

    public function updatePublish(Request $request, Course $course)
    {
		$record = $course;

		$record->release_flag = $request->release_flag;
		$record->wip_flag = $request->wip_flag;

		try
		{
			$record->save();
			logInfo(__FUNCTION__, 'Course status has been updated', ['id' => $course->id]);
		}
		catch (\Exception $e)
		{
			$msg = 'Error updating ' . TITLE_LC . ' status';
			logException(__FUNCTION__, $e->getMessage(), $msg, ['id' => $course->id]);
		}

		return redirect(REDIRECT_ADMIN);
    }

    public function rssReader()
    {
		$records = []; // make this countable so view will always work

		try
		{
			$records = Course::getRssReader();

			foreach($records as $course)
			{
				$sessions = [];

				foreach($course->lessons as $lesson)
				{
					if ($lesson->deleted_flag == 0)
					{
						if ($lesson->section_number == 1) // lesson 1 holds the chapter info
						{
							$sessions[$lesson->lesson_number]['title'] = isset($lesson->title_chapter) ? $lesson->title_chapter : 'Chapter ' . $lesson->lesson_number;
							$sessions[$lesson->lesson_number]['description'] = $lesson->description;
							$sessions[$lesson->lesson_number]['id'] = $lesson->id;
							$sessions[$lesson->lesson_number]['number'] = $lesson->lesson_number;
							$sessions[$lesson->lesson_number]['course'] = $course->title;
						}

						if (array_key_exists($lesson->lesson_number, $sessions) && array_key_exists('exercise_count', $sessions[$lesson->lesson_number]))
						{
							$sessions[$lesson->lesson_number]['exercise_count'] += 1;
						}
						else
						{
							$sessions[$lesson->lesson_number]['exercise_count'] = 1;
						}

						//dump($lesson->title . ': ' . $lesson->section_number);

					}
				}

				$course['sessions'] = $sessions;

				//dd($course);
			}
		}
		catch (\Exception $e)
		{
			dd($e);
			$msg = 'Error getting ' . TITLE_LC . ' rss';
			logException(__FUNCTION__, $e->getMessage(), $msg, ['id' => $course->id]);
		}

		//dd($records);

		return view(VIEWS . '.rss-reader', [
			'records' => $records,
		]);
	}

    public function rss()
    {
		$records = []; // make this countable so view will always work

		try
		{
			$records = Course::getRss();

			foreach($records as $course)
			{
				$sessions = [];

				foreach($course->lessons as $lesson)
				{
					//if ($lesson->lesson_number == 2) // test
					{
						if ($lesson->deleted_flag == 0)
						{
							if ($lesson->section_number == 1)
							{
								$sessions[$lesson->lesson_number]['title'] = isset($lesson->title_chapter) ? $lesson->title_chapter : 'Day ' . $lesson->lesson_number;
								$sessions[$lesson->lesson_number]['description'] = $lesson->description;
								$sessions[$lesson->lesson_number]['id'] = $lesson->id;
								$sessions[$lesson->lesson_number]['number'] = $lesson->lesson_number;
								$sessions[$lesson->lesson_number]['course'] = $course->title;
							}

							$breakSeconds = isset($lesson->break_seconds) ? $lesson->break_seconds : TIMED_SLIDES_DEFAULT_BREAK_SECONDS;
							$runSeconds = isset($lesson->seconds) ? $lesson->seconds : TIMED_SLIDES_DEFAULT_SECONDS;
							$seconds = $breakSeconds + $runSeconds;
							if (array_key_exists($lesson->lesson_number, $sessions) && array_key_exists('exercise_count', $sessions[$lesson->lesson_number]))
							{
								$sessions[$lesson->lesson_number]['exercise_count'] += 1;
								$sessions[$lesson->lesson_number]['seconds'] += $seconds;
							}
							else
							{
								$sessions[$lesson->lesson_number]['exercise_count'] = 1;
								$sessions[$lesson->lesson_number]['seconds'] = $seconds;
							}

							//dump($lesson->title . ': ' . $lesson->section_number . ", " . $runSeconds . ', ' . $breakSeconds . ', ' . $seconds);

						}
					}
				}

				$course['sessions'] = $sessions;

				//dd($course);
			}
		}
		catch (\Exception $e)
		{
			dd($e);
			$msg = 'Error getting ' . TITLE_LC . ' rss';
			logException(__FUNCTION__, $e->getMessage(), $msg);
		}

		return view(VIEWS . '.rss', [
			'records' => $records,
		]);
    }

    public function undelete(Request $request, $id)
    {
		$id = intval($id);

		try
		{
			$record = Course::withTrashed()
				->where('id', $id)
				->first();

			$record->restore();
			logInfo(LOG_CLASS, __('base.Record has been undeleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error finding deleted record'), ['record_id' => $id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

    public function deleted()
    {
		$records = []; // make this countable so view will always work

		try
		{
			$records = Course::withTrashed()
				->whereNotNull('deleted_at')
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

}
