<?php
/**  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *               
 * 
 */

namespace oat\taoWorkspace\controller;

use oat\tao\model\lock\LockManager;
/**
 * Sample controller
 *
 * @author Open Assessment Technologies SA
 * @package taoWorkspace
 * @license GPL-2.0
 *
 */
class Workspace extends \tao_actions_CommonModule {

    /**
     * initialize the services
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * A possible entry point to tao
     */
    public function checkout() {
        $currentUser = \common_session_SessionManager::getSession()->getUser();
        $resource = new \core_kernel_classes_Resource($this->getRequestParameter('id'));
        
        // lock instance
        LockManager::getImplementation()->setLock($resource, $currentUser->getIdentifier());
        
        // copy original to copy
        
        // map original to copy
        echo __("Rein");
    }

    public function checkin() {
        // verify lock
        // delete original
        // copy workspace to original
        // delete workspace
        // commit original
        // release lock
        echo __("Raus");
    }
}