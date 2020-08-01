<?php

/**
 * This file is part of a markocupic Contao Bundle
 *
 * @copyright  Marko Cupic 2020 <m.cupic@gmx.ch>
 * @author     Marko Cupic
 * @package    RSZ member listing
 * @license    MIT
 * @see        https://github.com/markocupic/rsz-member-listing-bundle
 *
 */

declare(strict_types=1);

namespace Markocupic\RszMemberListingBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\Database;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RszMemberListingModuleController
 *
 * @package Markocupic\RszMemberListingBundle\Controller\FrontendModule
 */
class RszMemberListingModuleController extends AbstractFrontendModuleController
{
    /**
     * @var PageModel
     */
    protected $page;

    /**
     * RszMemberListingModuleController constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Lazyload some services
     *
     * @return array
     */
    public static function getSubscribedServices(): array
    {
        $services = parent::getSubscribedServices();

        //$services['contao.framework'] = ContaoFramework::class;
        //$services['database_connection'] = Connection::class;
        //$services['contao.routing.scope_matcher'] = ScopeMatcher::class;
        //$services['security.helper'] = Security::class;
        //$services['translator'] = TranslatorInterface::class;

        return $services;
    }

    /**
     * This method extends the parent __invoke method,
     * its usage is usually not necessary
     *
     * @param Request $request
     * @param ModuleModel $model
     * @param string $section
     * @param array|null $classes
     * @param PageModel|null $page
     * @return Response
     */
    public function __invoke(Request $request, ModuleModel $model, string $section, array $classes = null, PageModel $page = null): Response
    {
        return parent::__invoke($request, $model, $section, $classes);
    }

    /**
     * @param Template $template
     * @param ModuleModel $model
     * @param Request $request
     * @return null|Response
     */
    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $arrMembers = [];
        $objMembers = Database::getInstance()
            ->prepare("SELECT * FROM tl_user WHERE isRSZ=? AND disable=? ORDER BY fe_sorting ASC, trainerFromGroup DESC, dateOfBirth")
            ->execute('1', '');

        while ($objMembers->next())
        {
            $hasSteckbrief = null;
            $objSteckbrief = Database::getInstance()
                ->prepare("SELECT * FROM tl_rsz_steckbrief WHERE pid=? AND aktiv=?")
                ->execute($objMembers->id, 1);
            if ($objSteckbrief->numRows)
            {
                $hasSteckbrief = true;
            }

            $arrMembers[] = [
                'hasSteckbrief'         => $hasSteckbrief,
                'id'                    => StringUtil::specialchars($objMembers->id),
                'name'                  => StringUtil::specialchars($objMembers->name),
                'funktion'              => $objMembers->funktion != "" ? StringUtil::specialchars(implode(", ", StringUtil::deserialize($objMembers->funktion,true))) : "",
                'gender'                => StringUtil::specialchars($objMembers->gender),
                'funktionsbeschreibung' => StringUtil::specialchars($objMembers->funktionsbeschreibung),
                'link_digitalrock'      => $objMembers->link_digitalrock,
                'nationalmannschaft'    => StringUtil::specialchars($objMembers->nationalmannschaft),
                'kategorie'             => StringUtil::specialchars($objMembers->kategorie),
                'niveau'                => StringUtil::specialchars($objMembers->niveau),
                'trainingsgruppe'       => trim($objMembers->trainingsgruppe),
                'trainerFromGroup'      => trim($objMembers->trainerFromGroup),
            ];
        }

        $template->arrMembers = $arrMembers;

        return $template->getResponse();
    }
}
