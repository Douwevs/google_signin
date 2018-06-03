<?php

namespace GeorgRinger\GoogleSignin\Utility;

/**
 * This file is part of the "google_signin" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Create custom fields for extension configuration
 */
class ExtensionManagerConfigurationUtility
{
    public function getBackendUserTemplate(array $params): string
    {
        $userRows = $this->getBeUsers((int)$params['fieldValue']);

        $html = '<select class="form-control" name="' . $params['fieldName'] . '"><option value="0"></option>';
        if ($userRows[1]) {
            $label = $this->translate('em.beAutomaticUserCreationTemplate.admins');
            $html .= '<optgroup label="' . $label . '">' . implode(LF, $userRows[1]) . '</optgroup>';
        }
        if ($userRows[0]) {
            $label = $this->translate('em.beAutomaticUserCreationTemplate.editors');
            $html .= '<optgroup label="' . $label . '">' . implode(LF, $userRows[0]) . '</optgroup>';
        }
        $html .= '</select>';

        return $html;
    }

    /**
     * Get all BE users, split by if admin or not
     *
     * @param int $selected selected id
     * @return array
     */
    protected function getBeUsers(int $selected): array
    {
        $rows = [];
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_users');

        $tempRows = $qb
            ->select('username', 'uid', 'realName', 'admin')
            ->from('be_users')
            ->orderBy('username')
            ->execute()
            ->fetchAll();

        foreach ($tempRows as $row) {
            $isSelected = (int)$row['uid'] === $selected ? ' selected="selected" ' : '';
            $title = htmlspecialchars($row['username']);
            if ($row['realName']) {
                $title .= ' (' . htmlspecialchars($row['realName']) . ')';
            }
            $rows[(int)$row['admin']][$row['uid']] = '<option ' . $isSelected . 'value="' . $row['uid'] . '">' . $title . '</option>';
        }

        return $rows;
    }

    public function getFrontendUserTemplate(array $params): string
    {
        $userRows = $this->getFeUsers((int)$params['fieldValue']);

        $html = '<select class="form-control" name="' . $params['fieldName'] . '"><option value="0"></option>';
        $html .= implode(LF, $userRows);
        $html .= '</select>';

        return $html;
    }

    /**
     * Get all BE users, split by if admin or not
     *
     * @param int $selected selected id
     * @return array
     */
    protected function getFeUsers(int $selected): array
    {
        $rows = [];
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('fe_users');

        $tempRows = $qb
            ->select('username', 'uid', 'name')
            ->from('fe_users')
            ->orderBy('username')
            ->execute()
            ->fetchAll();

        foreach ($tempRows as $row) {
            $isSelected = (int)$row['uid'] === $selected ? ' selected="selected" ' : '';
            $title = htmlspecialchars($row['username']);
            if ($row['name']) {
                $title .= ' (' . htmlspecialchars($row['name']) . ')';
            }
            $rows[$row['uid']] = '<option ' . $isSelected . 'value="' . $row['uid'] . '">' . $title . '</option>';
        }

        return $rows;
    }

    protected function translate(string $key): string
    {
        return htmlspecialchars($GLOBALS['LANG']->sL('LLL:EXT:google_signin/Resources/Private/Language/locallang.xlf:' . $key));
    }
}
