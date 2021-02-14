<?php

namespace App;

// Release Status

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
		WIP_NOTSET => 'Not Set',
		WIP_INACTIVE => 'Inactive',
		WIP_DEV => 'Dev',
		WIP_TEST => 'Test',
		WIP_FINISHED => 'Finished',
	];

    static public function isFinished($wip_flag)
    {
		return ($wip_flag == WIP_FINISHED);
	}
    static public function isPublished($release_flag)
    {
		return ($release_flag == RELEASE_PUBLIC);
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
        $rc['class'] = $class[$flag];

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

        $rc['label'] = self::$_wipFlags[$flag];
        $rc['class'] = $class[$flag];
        $rc['done'] = $flag >= WIP_FINISHED;

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
