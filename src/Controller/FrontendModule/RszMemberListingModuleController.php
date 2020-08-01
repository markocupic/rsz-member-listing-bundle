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
use Contao\CoreBundle\Framework\ContaoFramework;
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

        $services['contao.framework'] = ContaoFramework::class;
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
        /** @var PageModel $pageModelAdapter */
        $pageModelAdapter = $this->get('contao.framework')->getAdapter(PageModel::class);

        /** @var StringUtil $stringUtilAdapter */
        $stringUtilAdapter = $this->get('contao.framework')->getAdapter(StringUtil::class);

        /** @var Database $databaseAdapter */
        $databaseAdapter = $this->get('contao.framework')->getAdapter(Database::class);

        // Get portrait reader page
        $objJumpTo = $pageModelAdapter->findByPk($model->rszSteckbriefReaderPage);

        $arrUsers = [];
        $objUser = $databaseAdapter->getInstance()
            ->prepare("SELECT * FROM tl_user WHERE isRSZ=? AND disable=? ORDER BY fe_sorting, trainerFromGroup DESC, dateOfBirth")
            ->execute('1', '');

        while ($objUser->next())
        {
            $hasSteckbrief = false;
            $objSteckbrief = $databaseAdapter->getInstance()
                ->prepare("SELECT * FROM tl_rsz_steckbrief WHERE pid=? AND aktiv=?")
                ->execute($objUser->id, 1);

            if ($objSteckbrief->numRows)
            {
                $hasSteckbrief = true;
            }

            $arrUsers[] = [
                'hasSteckbrief'         => $hasSteckbrief && $objJumpTo !== null,
                'portraitHref'          => ($hasSteckbrief && $objJumpTo !== null) ? $objJumpTo->getFrontendUrl('/' . $objUser->username) : null,
                'id'                    => $stringUtilAdapter->specialchars($objUser->id),
                'name'                  => $stringUtilAdapter->specialchars($objUser->name),
                'funktion'              => $objUser->funktion != "" ? $stringUtilAdapter->specialchars(implode(", ", $stringUtilAdapter->deserialize($objUser->funktion, true))) : "",
                'gender'                => $stringUtilAdapter->specialchars($objUser->gender),
                'funktionsbeschreibung' => $stringUtilAdapter->specialchars($objUser->funktionsbeschreibung),
                'link_digitalrock'      => $objUser->link_digitalrock,
                'nationalmannschaft'    => $stringUtilAdapter->specialchars($objUser->nationalmannschaft),
                'kategorie'             => $stringUtilAdapter->specialchars($objUser->kategorie),
                'niveau'                => $stringUtilAdapter->specialchars($objUser->niveau),
                'trainingsgruppe'       => trim((string) $objUser->trainingsgruppe),
                'trainerFromGroup'      => trim((string) $objUser->trainerFromGroup),
            ];
        }

        $template->arrUsers = $arrUsers;

        return $template->getResponse();
    }
}
