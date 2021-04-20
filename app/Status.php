<?php

namespace App;

use Auth;

//
// Handles Release and WIP Status
//
class Status
{
    static private $_releaseFlags = [
		RELEASEFLAG_NOTSET => 'ui.Not Set',
		RELEASEFLAG_PRIVATE => 'ui.Private',
		RELEASEFLAG_APPROVED => 'ui.Approved',
		RELEASEFLAG_PAID => 'ui.Premium',
		RELEASEFLAG_MEMBER => 'ui.Member',
		RELEASEFLAG_PUBLIC => 'ui.Public',
    ];

	static private $_wipFlags = [
		WIP_NOTSET => 'ui.Not Set',
		WIP_INACTIVE => 'ui.Inactive',
		WIP_DEV => 'ui.Development',
		WIP_TEST => 'ui.Test',
		WIP_FINISHED => 'ui.Finished',
	];

    static public function isFinished($wip_flag)
    {
		return ($wip_flag == WIP_FINISHED);
	}

    static public function isPublic($release_flag)
    {
		return ($release_flag == RELEASEFLAG_PUBLIC);
	}

    static public function isPrivate($release_flag)
    {
		return ($release_flag == RELEASEFLAG_PRIVATE);
	}

    static public function canShow($release_flag, $release)
    {
        $rc = false;

        if ($release == 'private')
        {
            if ($release_flag <= RELEASEFLAG_APPROVED)
                $rc = true;
        }
        else if ($release == 'public')
        {
            if (Auth::check())
            {
                if ($release_flag >= RELEASEFLAG_PAID)
                    $rc = true;
            }
            else
            {
                if ($release_flag >= RELEASEFLAG_PUBLIC)
                    $rc = true;
            }
        }

		return $rc;
	}

	static public function getReleaseFlag()
	{
		$rc = RELEASEFLAG_PUBLIC;

		if (isAdmin()) // admin sees all
		{
			$rc = RELEASEFLAG_NOTSET;
		}
		//todo: else if (isPaid()) // paid member
		//{
		//	$rc = RELEASEFLAG_PAID;
		//}
		else if (Auth::check()) // member logged in_array
		{
			$rc = RELEASEFLAG_MEMBER;
		}

		return $rc;
	}

    static public function getReleaseFlags()
    {
		return self::$_releaseFlags;
	}

    static public function getWipFlags()
    {
		return self::$_wipFlags;
	}

    static public function getReleaseStatus($flag)
    {
        $class = [
            RELEASEFLAG_NOTSET => 'btn-secondary',
            RELEASEFLAG_PRIVATE => 'btn-secondary',
            RELEASEFLAG_APPROVED => 'btn-secondary',
            RELEASEFLAG_PAID => 'btn-primary',
            RELEASEFLAG_MEMBER => 'btn-primary',
            RELEASEFLAG_PUBLIC => 'btn-success',
        ];

        $rc['label'] = self::$_releaseFlags[$flag];
        $rc['text'] = self::$_releaseFlags[$flag]; //dupe for backwards compatibility
        $rc['class'] = $class[$flag];
        $rc['btn'] = $class[$flag];
        $rc['public'] = ($flag >= RELEASEFLAG_PUBLIC);

        return $rc;
    }

    static public function getWipStatus($flag)
    {
        $class = [
            WIP_NOTSET => 'btn-secondary',
            WIP_INACTIVE => 'btn-secondary',
            WIP_DEV => 'btn-secondary',
            WIP_TEST => 'btn-primary',
            WIP_FINISHED => 'btn-primary',
            WIP_DEFAULT => 'btn-success',
        ];

        if (isset($flag))
        {
            $rc['label'] = self::$_wipFlags[$flag];
            $rc['text'] = self::$_wipFlags[$flag];
            $rc['class'] = $class[$flag];
            $rc['done'] = $flag >= WIP_FINISHED;
            $rc['btn'] = $class[$flag];
        }
        else
        {
            $rc['label'] = '';
            $rc['text'] = '';
            $rc['class'] = '';
            $rc['done'] = false;
            $rc['btn'] = '';
        }

        return $rc;
    }

    static public function getReleaseFlagForUserLevel()
    {
        return isAdmin() ? RELEASEFLAG_NOTSET : RELEASEFLAG_PUBLIC;
    }

    static public function getConditionForUserLevel()
    {
        return isAdmin() ? '>=' : '=';
    }

}
