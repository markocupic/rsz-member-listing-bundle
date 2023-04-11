<?php

declare(strict_types=1);

/*
 * This file is part of RSZ Member Listing Bundle.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/rsz-member-listing-bundle
 */

use Markocupic\RszMemberListingBundle\Controller\FrontendModule\RszMemberListingController;

/**
 * Frontend modules
 */
$GLOBALS['TL_LANG']['FMD']['rsz_frontend_modules'] = 'RSZ Frontendmodule';
$GLOBALS['TL_LANG']['FMD'][RszMemberListingController::TYPE] = ['RSZ Mitgliederauflistung', 'Erstellen Sie eine RSZ Mitgliederliste'];
