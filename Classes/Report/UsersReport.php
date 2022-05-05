<?php
namespace Qc\QcInfoRights\Report;

use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Beuser\Domain\Model\Demand;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class UsersReport extends \Qc\QcInfoRights\Report\QcInfoRightsReport
{
    /**
     * Array configuration for the order of Table backend user list
     */
    protected const ORDER_BY_VALUES = [
        'lastLogin' => [
            ['lastlogin', 'ASC'],
        ],
        'lastLogin_reverse' => [
            ['lastlogin', 'DESC'],
        ],
        'userName' => [
            ['userName', 'ASC'],
        ],
        'userName_reverse' => [
            ['userName', 'DESC'],
        ],
        'email' => [
            ['email' , 'ASC'],
        ],
        'email_reverse' => [
            ['email' , 'DESC'],
        ],
        'disable_compare' => [
            ['disable' , 'ASC'],
        ],
        'disable_compare_reverse' => [
            ['disable' , 'DESC'],
        ],
    ];

    /**
     * Create tabs to split the report and the checkLink functions
     * @throws RouteNotFoundException
     */
    protected function renderContent(): string
    {
        if (!$this->isAccessibleForCurrentUser) {
            // If no access or if ID == zero
            $this->moduleTemplate->addFlashMessage(
                $this->getLanguageService()->getLL('no.access'),
                $this->getLanguageService()->getLL('no.access.title'),
                FlashMessage::ERROR
            );
            return '';
        }

        $menuItems = [];
        if ($this->showTabUsers) {
            $menuItems[] = [
                'label' => $this->getLanguageService()->getLL('beUserLists'),
                'content' => $this->createViewForBeUserListTab()->render()
            ];
        }

        return $this->moduleTemplate->getDynamicTabMenu($menuItems, 'report-qcinforights');
    }

    /**
     * Displays the View for the Backend User List
     *
     * @return StandaloneView
     * @throws RouteNotFoundException
     */
    protected function createViewForBeUserListTab(): StandaloneView
    {
        $prefix = "user";
        $this->setPageInfo();

        $view = $this->createView('BeUserList');

        $demand = $this->moduleData->getDemand();
        $demand->setRejectUserStartWith('_');

        $orderArray = self::ORDER_BY_VALUES[$this->orderBy] ?? [];

        if(!empty($orderArray)){
            $this->filter->setOrderArray($orderArray);
        }

        if(!$this->showAdministratorUser){
            $demand->setUserType(Demand::USERTYPE_USERONLY);
        }

        // Filter
        if($this->set['username'] != null && !empty($this->set['username'])){
            $this->filter->setUsername($this->set['username']);
            $this->filter->setCurrentUsersTabPage(1);
        }
        if($this->set['mail'] != null && !empty($this->set['mail'])){
            $this->filter->setMail($this->set['mail']);
            $this->filter->setCurrentUsersTabPage(1);
        }
        if(!empty($this->set['hideInactif']) && (int)($this->set['hideInactif']) == 1){
            $this->filter->setHideInactiveUsers(Demand::STATUS_ACTIVE);
        }
        // Reset from form
        if($this->set['filterSearch'] == 1){
            if(empty($this->set['username'])){
                $this->filter->setUsername('');
            }
            if(empty($this->set['mail'])){
                $this->filter->setMail('');
            }
            if(empty($this->set['hideInactif'])){
                $this->filter->setHideInactiveUsers(0);
            }
            $this->filter->setCurrentUsersTabPage(1);
        }

        if (GeneralUtility::_GP('userPaginationPage') != null ){
            $userPaginationCurrentPage = (int)GeneralUtility::_GP('userPaginationPage');
            // Store the current page on session
            $this->filter->setCurrentUsersTabPage($userPaginationCurrentPage);
        }
        else{
            // read from Session
            $userPaginationCurrentPage = $this->filter->getCurrentUsersTabPage();
        }

        $this->updateFilter();
        $filterArgs = [
            'username' => $this->backendSession->get('qc_info_rights_key')->getUsername(),
            'mail' => $this->backendSession->get('qc_info_rights_key')->getMail(),
            'hideInactif' => $this->backendSession->get('qc_info_rights_key')->getHideInactiveUsers()
        ];
        $demand = $this->mapFilterToDemand($this->backendSession->get('qc_info_rights_key'));
        /**Implement tableau Header withDynamically order By Field*/
        foreach (array_keys(self::ORDER_BY_VALUES) as $key) {
            $sortActions[$key] = $this->constructBackendUri(['orderBy' => $key]);
        }
        $tabHeaders = $this->getVariablesForTableHeader($sortActions);
        $pagination = $this->getPagination($this->backendUserRepository->findDemanded($demand), $userPaginationCurrentPage,$this->usersPerPage );// we assign the groupsCurrentPaginationPage and usersCurrentPaginationPage to keep the pagination for each tab separated

        $view->assignMultiple([
            'prefix' => 'beUserList',
            'backendUsers' => $pagination['paginatedData'],
            'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
            'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
            'showExportUsers' => $this->showExportUsers,
            'args' => $filterArgs,
            'tabHeader' => $tabHeaders,
            'pagination' => $pagination['pagination'],
            'currentPage' => $this->id
        ]);
        return $view;
    }

    /**
     * @param array<string,mixed> $additionalQueryParameters
     * @param string $route
     * @return string
     * @throws RouteNotFoundException
     */
    protected function constructBackendUri(array $additionalQueryParameters = [], string $route = 'web_info'): string
    {
        $parameters = [
            'id' => $this->id,
            'depth' => $this->depth,
            'orderBy' => $this->orderBy,
            self::prefix_filter.'_SET[username]' => $this->set['username'],
            self::prefix_filter.'_SET[mail]' => $this->set['mail'],
            self::prefix_filter.'_SET[hideInactif]' => $this->set['hideInactif'],
            self::prefix_filter.'_SET[filterSearch]' => $this->set['filterSearch'],
        ];

        // if same key, additionalQueryParameters should overwrite parameters
        $parameters = array_merge($parameters, $additionalQueryParameters);

        /**
         * @var UriBuilder $uriBuilder
         */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $uri = (string)$uriBuilder->buildUriFromRoute($route, $parameters);

        return $uri;
    }

    /**
     * Sets variables for the Fluid Template of the table with the Backend User List
     * @param array<string,string> $sortActions
     * @return mixed[] variables
     */
    protected function getVariablesForTableHeader(array $sortActions): array
    {
        $languageService = $this->getLanguageService();

        $headers = [
            'userName',
            'email',
            'lastLogin',
            'disable_compare'
        ];

        $tableHeadData = [];

        foreach ($headers as $key) {
            $tableHeadData[$key]['label'] = $languageService->sL(self::prefix_be_user_lang.$key);
            if (isset($sortActions[$key])) {
                // sorting available, add url
                if ($this->orderBy === $key) {
                    $tableHeadData[$key]['url'] = $sortActions[$key . '_reverse'] ?? '';
                } else {
                    $tableHeadData[$key]['url'] = $sortActions[$key] ?? '';
                }

                // add icon only if this is the selected sort order
                if ($this->orderBy === $key) {
                    $tableHeadData[$key]['icon'] = 'status-status-sorting-asc';
                }elseif ($this->orderBy === $key . '_reverse') {
                    $tableHeadData[$key]['icon'] = 'status-status-sorting-desc';
                }
            }
        }

        $tableHeaderHtml = [];
        foreach ($tableHeadData as $key => $values) {
            if (isset($values['url'])) {
                $tableHeaderHtml[$key]['header'] = sprintf(
                    '<a href="%s" style="text-decoration: underline;">%s</a>',
                    $values['url'],
                    $values['label']
                );
            } else {
                $tableHeaderHtml[$key]['header'] = $values['label'];
            }

            if (($values['icon'] ?? '') !== '') {
                $tableHeaderHtml[$key]['icon'] = $values['icon'];
            }
        }
        return $tableHeaderHtml;
    }



}
