<?php

/**
 * F1 Doc Gen
 * @author Daniel Boorn - daniel.boorn@gmail.com
 * @copyright Daniel Boorn
 * @license Creative Commons Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @namespace F1
 */

/**
 * 6/13/2013 - Daniel Boorn
 * This class takes the API docs and attempts to generate an JSON file with a list of API endpoints.
 * No need to use this unless you want to replace the existing json file included with the package.
 * Use as own risk! Docs are not perfect! Package json has been corrected. Contribute corrections please!
 */


namespace F1;


class DocGen
{

    public $list = array();

    public $baseUrl = 'https://demo.fellowshiponeapi.com';
    public $verbs = array('GET', 'POST', 'PUT', 'DELETE');
    public $hashs = array("search", "list", "show", "edit", "new", "create", "update");
    public $remove = array("{parameters}", "?");
    public $pattern = '/\[([GETPSUDLO]+)\](.*)/';

    public $docPages = array(
        'http://developer.fellowshipone.com/docs/v1/Households.help',
        'http://developer.fellowshipone.com/docs/v1/People.help',
        'http://developer.fellowshipone.com/docs/v1/Addresses.help',
        'http://developer.fellowshipone.com/docs/v1/People/AttributeGroups.help',
        'http://developer.fellowshipone.com/docs/v1/People/AttributeGroups/0/Attributes.help',
        'http://developer.fellowshipone.com/docs/v1/Communications.help',
        'http://developer.fellowshipone.com/docs/v1/Communications/CommunicationTypes.help',
        'http://developer.fellowshipone.com/docs/v1/People/Denominations.help',
        'http://developer.fellowshipone.com/docs/v1/People/Occupations.help',
        'http://developer.fellowshipone.com/docs/v1/People/Schools.help',
        'http://developer.fellowshipone.com/docs/v1/People/Statuses.help',
        'http://developer.fellowshipone.com/docs/v1/People/Statuses/0/SubStatuses.help',
        'http://developer.fellowshipone.com/docs/v1/Requirements.help',
        'http://developer.fellowshipone.com/docs/v1/Requirements/RequirementStatuses.help',
        'http://developer.fellowshipone.com/docs/v1/Requirements/BackgroundCheckStatuses.help',
        'http://developer.fellowshipone.com/docs/v1/People/PeopleRequirements.help',
        'http://developer.fellowshipone.com/docs/v1/Requirements/Documents.help',
        'http://developer.fellowshipone.com/docs/giving/v1/Accounts.help',
        'http://developer.fellowshipone.com/docs/giving/v1/Accounts/AccountTypes.help',
        'http://developer.fellowshipone.com/docs/giving/v1/Batches.help',
        'http://developer.fellowshipone.com/docs/giving/v1/Batches/BatchTypes.help',
        'http://developer.fellowshipone.com/docs/giving/v1/ContributionReceipts.help',
        'http://developer.fellowshipone.com/docs/giving/v1/ContributionTypes.help',
        'http://developer.fellowshipone.com/docs/giving/v1/Funds.help',
        'http://developer.fellowshipone.com/docs/giving/v1/Funds/FundTypes.help',
        'http://developer.fellowshipone.com/docs/giving/v1/PledgeDrives.help',
        'http://developer.fellowshipone.com/docs/giving/v1/RDCBatches.help',
        'http://developer.fellowshipone.com/docs/giving/v1/ReferenceImages.help',
        'http://developer.fellowshipone.com/docs/giving/v1/SubFunds.help',
        'http://developer.fellowshipone.com/docs/groups/v1/Groups.help',
        'http://developer.fellowshipone.com/docs/groups/v1/Members.help',
        'http://developer.fellowshipone.com/docs/groups/v1/MemberTypes.help',
        'http://developer.fellowshipone.com/docs/groups/v1/Prospects.help',
        'http://developer.fellowshipone.com/docs/groups/v1/Events.help',
        'http://developer.fellowshipone.com/docs/groups/v1/DateRangeTypes.help',
        'http://developer.fellowshipone.com/docs/groups/v1/Genders.help',
        'http://developer.fellowshipone.com/docs/groups/v1/MaritalStatuses.help',
        'http://developer.fellowshipone.com/docs/groups/v1/Timezones.help',
        'http://developer.fellowshipone.com/docs/events/v1/Events.help',
        'http://developer.fellowshipone.com/docs/events/v1/Schedules.help',
        'http://developer.fellowshipone.com/docs/events/v1/Locations.help',
        'http://developer.fellowshipone.com/docs/events/v1/RecurrenceTypes.help',
    );

    /**
     * construct
     */
    public function __construct()
    {

    }

    /**
     * forge factory
     * @param array $settings =null
     * @returns void
     */
    public function forge()
    {
        return new self();
    }

    /**
     * generate method id from hash name and path
     * @param string $hash
     * @param string $path
     */
    public function genMethodId($hash, $path)
    {
        $parts = explode("/", trim($path));
        $idList = array();
        $load = false;
        foreach ($parts as $p) {
            if (strtolower($p) == "v1") {
                $load = true;
                continue;
            }
            if (strlen($p) == 0 || $p{0} == "{") continue;
            if ($load) $idList[] = strtolower($p);
        }
        if (end($idList) != $hash) $idList[] = $hash;
        return implode("_", $idList);
    }

    /**
     * clean base path
     * @param string $path
     */
    public function cleanpath($path)
    {
        return trim(str_replace(array($this->baseUrl, "[POST(Low REST)]"), "", $path));
    }

    /**
     * generate wsd array from HTML Doc
     * @returns array $list
     */
    public function getWSD()
    {
        foreach ($this->docPages as $url) {
            $doc = \phpQuery::newDocumentHTML(file_get_contents($url));
            foreach ($this->hashs as $hashtag) {
                #var_dump($hashtag);
                $pathStr = str_replace($this->remove, "", trim($doc["a[href='#{$hashtag}'"]->parent()->find("a")->remove()->end()->text()));
                #var_dump($pathStr);
                $matches = null;
                $r = preg_match($this->pattern, $pathStr, $matches);
                #var_dump($matches);
                if (count($matches) != 3) continue;
                $id = $this->genMethodId($hashtag, $matches[2]);
                $this->list[$id] = array('verb' => $matches[1], 'path' => $this->cleanPath($matches[2]));
            }
        }
        return $this->list;
    }

    /**
     * save generated wsd json to filename
     * @param string $filename
     * @returns result
     */
    public function saveWSD($filename)
    {
        return file_put_contents($filename, json_encode($this->getWSD()));
    }

}
