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

namespace Markocupic\RszMemberListingBundle\Controller\FrontendModule;

use Contao\Config;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\Template;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(RszMemberListingController::TYPE, category: 'rsz_frontend_modules')]
class RszMemberListingController extends AbstractFrontendModuleController
{
    public const TYPE = 'rsz_member_listing';

    private readonly Adapter $stringUtil;
    private readonly Adapter $pageModel;

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly Connection $connection,
    ) {
        $this->stringUtil = $this->framework->getAdapter(StringUtil::class);
        $this->pageModel = $this->framework->getAdapter(PageModel::class);
    }

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        // Get portrait reader page
        $objJumpTo = $this->pageModel->findByPk($model->rszSteckbriefReaderPage);

        $arrUsers = [];
        $users = $this->connection->fetchAllAssociative(
            'SELECT * FROM tl_user WHERE isRSZ = ? AND disable = ? ORDER BY fe_sorting, trainerFromGroup DESC, dateOfBirth',
            [
                '1',
                '',
            ],
        );

        foreach ($users as $user) {
            $hasProfile = false;
            $profile = $this->connection->fetchAssociative(
                'SELECT * FROM tl_rsz_steckbrief WHERE pid = ? AND aktiv = ?',
                [
                    $user['id'],
                    '1',
                ],
            );

            if (false !== $profile) {
                $hasProfile = true;
            }

            $arrUsers[] = [
                'id' => $this->stringUtil->specialchars($user['id']),
                'hasSteckbrief' => $hasProfile && null !== $objJumpTo,
                'portraitHref' => $hasProfile && null !== $objJumpTo ? $objJumpTo->getFrontendUrl('/'.$user['username']) : null,
                'name' => $this->stringUtil->specialchars($user['name']),
                'funktion' => '' !== $user['funktion'] ? $this->stringUtil->specialchars(implode(', ', $this->stringUtil->deserialize($user['funktion'], true))) : '',
                'gender' => $this->stringUtil->specialchars($user['gender']),
                'funktionsbeschreibung' => $this->stringUtil->specialchars($user['funktionsbeschreibung']),
                'link_digitalrock' => $user['link_digitalrock'],
                'nationalmannschaft' => (bool) $user['nationalmannschaft'],
                'kategorie' => $this->stringUtil->specialchars($user['kategorie']),
                'niveau' => $this->stringUtil->specialchars($user['niveau']),
                'trainingsgruppe' => trim((string) $user['trainingsgruppe']),
                'trainerFromGroup' => $this->stringUtil->deserialize($user['trainerFromGroup'], true),
            ];
        }

        $template->set('training_groups', StringUtil::trimsplit(',', Config::get('mcupic_be_benutzerverwaltung_trainingsgruppe')));

        $template->set('users',  $arrUsers);

        return $template->getResponse();
    }
}
