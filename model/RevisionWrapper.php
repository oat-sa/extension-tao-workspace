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
 */
namespace oat\taoWorkspace\model;

use common_exception_Error;
use common_session_SessionManager;
use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\service\ConfigurableService;
use oat\taoRevision\model\RepositoryInterface;
use oat\taoRevision\model\Revision;
use oat\tao\model\lock\LockManager;
use oat\taoRevision\model\RevisionNotFoundException;
use core_kernel_classes_Resource as Resource;
use oat\taoWorkspace\model\lockStrategy\ApplicableLockInterface;

class RevisionWrapper extends ConfigurableService implements RepositoryInterface
{
    use OntologyAwareTrait;

    public const OPTION_INNER_IMPLEMENTATION = 'inner';
    
    /**
     * @return RepositoryInterface
     */
    protected function getInner()
    {
        return $this->getServiceLocator()->get($this->getOption(self::OPTION_INNER_IMPLEMENTATION));
    }

    /**
     * @param string $resourceId
     *
     * @return Revision[]
     */
    public function getAllRevisions(string $resourceId)
    {
        return $this->getInner()->getAllRevisions($resourceId);
    }

    /**
     * @param string $resourceId
     * @param int    $version
     *
     * @return Revision
     * @throws RevisionNotFoundException
     */
    public function getRevision(string $resourceId, int $version)
    {
        return $this->getInner()->getRevision($resourceId, $version);
    }

    /**
     * @param Resource    $resource
     * @param string      $message
     * @param int|null    $version
     * @param string|null $userId
     *
     * @return Revision
     * @throws common_exception_Error
     */
    public function commit(Resource $resource, string $message, int $version = null, string $userId = null)
    {
        $userId = common_session_SessionManager::getSession()->getUser()->getIdentifier();

        if (is_null($userId)) {
            throw new \common_exception_Error('Anonymous User cannot commit resources');
        }

        $lockManager = LockManager::getImplementation();

        if ($lockManager->isLocked($resource)) {
            if ($lockManager instanceof ApplicableLockInterface) {
                $lockManager->apply($resource, $userId, true);
            }
        }

        return $this->getInner()->commit($resource, $message, $version);
    }

    /**
     * @param Revision $revision
     *
     * @return bool
     * @throws common_exception_Error
     */
    public function restore(Revision $revision)
    {
        $resource = $this->getResource($revision->getResourceId());

        $lockManager = LockManager::getImplementation();

        if ($lockManager->isLocked($resource)) {
            $userId = common_session_SessionManager::getSession()->getUser()->getIdentifier();
            $lockManager->releaseLock($resource, $userId);
        }

        return $this->getInner()->restore($revision);
    }
}
