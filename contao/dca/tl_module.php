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

$GLOBALS['TL_DCA']['tl_module']['palettes'][RszMemberListingController::TYPE] = '
{title_legend},name,headline,type;
{config_legend},rszSteckbriefReaderPage;
{template_legend:hide},customTpl;
{protected_legend:hide},protected;
{expert_legend:hide},guests,cssID
';
