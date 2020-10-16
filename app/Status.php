<?php

namespace App;

// Release Status

class Status
{
	const RELEASE_NOTSET = 0;
	const RELEASE_PRIVATE = 10;	// visible for owner only and can't be promoted until approved
	const RELEASE_APPROVED = 20;	// visible for owner only and can be promoted
	const RELEASE_PAID = 50; 	// visible for logged-in paid members
	const RELEASE_MEMBER = 80; // visible for logged-in members
	const RELEASE_PUBLIC = 100;	// visible for all

	// Work in progress
	const WIP_NOTSET = 0;
	const WIP_INACTIVE = 10;
	const WIP_DEV = 20;
	const WIP_TEST = 30;
	const WIP_FINISHED = 100;
	
    static private $_releaseFlags = [
		self::RELEASE_NOTSET => 'Not Set',
		self::RELEASE_PRIVATE => 'Private',
		self::RELEASE_APPROVED => 'Approved',
		self::RELEASE_PAID => 'Premium',
		self::RELEASE_MEMBER => 'Member',
		self::RELEASE_PUBLIC => 'Public',
    ];

	static private $_wipFlags = [
		self::WIP_NOTSET => 'Not Set',
		self::WIP_INACTIVE => 'Inactive',
		self::WIP_DEV => 'Dev',
		self::WIP_TEST => 'Test',
		self::WIP_FINISHED => 'Finished',
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

    static public function getReleaseStatus($release_flag, $showPublic = false)
    {
		$btn = '';
		$text = Tools::safeArrayGetString(self::$_releaseFlags, $release_flag, 'Unknown Value: ' . $release_flag);
		$done = false;

		switch ($release_flag)
		{
			case RELEASE_NOTSET:
				$btn = 'btn-danger';
				break;
			case RELEASE_PRIVATE:
				$btn = 'btn-secondary';
				break;
			case RELEASE_APPROVED:
				$btn = 'btn-warning';
				break;
			case RELEASE_PAID:
				$btn = 'btn-info';
				break;
			case RELEASE_MEMBER:
				$btn = 'btn-primary';
				break;
			case RELEASE_PUBLIC:
			{
				if ($showPublic)
				{
					$btn = 'btn-success';
				}
				else
				{
					// don't show anything for published records
					$btn = '';
					$text = '';
					$done = true;
				}
				break;
			}
			default:
				$btn = 'btn-danger';
				$text = 'Not Set';
				break;
		}

		return [
				'btn' => $btn,
				'text' => $text,
				'done' => $done,
			];
	}

    static public function getWipStatus($wip_flag)
    {
		$btn = '';
		$text = Tools::safeArrayGetString(self::$_wipFlags, $wip_flag, 'Unknown Value: ' . $wip_flag);
        $done = false;

		switch ($wip_flag)
		{
			case WIP_NOTSET:
				$btn = 'btn-danger';
				break;
			case WIP_INACTIVE:
				$btn = 'btn-info';
				break;
			case WIP_DEV:
				$btn = 'btn-warning';
				break;
			case WIP_TEST:
				$btn = 'btn-primary';
				break;
			case WIP_FINISHED:
				// don't show anything for finished records
				$btn = '';
				$text = '';
				$done = true;
				break;
			default:
				$btn = 'btn-danger';
				$text = 'Unknown Value';
				break;
		}

		return [
				'btn' => $btn,
				'text' => $text,
				'done' => $done,
			];
	}

}
