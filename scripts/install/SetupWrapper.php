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
namespace oat\taoWorkspace\scripts\install;

use common_Exception;
use common_persistence_Manager;
use core_kernel_persistence_smoothsql_SmoothModel as SmoothModel;
use oat\generis\model\data\Model;
use oat\generis\model\data\ModelManager;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\generis\persistence\PersistenceManager;
use oat\oatbox\extension\InstallAction;
use oat\oatbox\service\exception\InvalidServiceManagerException;
use oat\tao\model\lock\LockManager;
use oat\taoRevision\model\Repository;
use oat\taoRevision\model\RepositoryInterface;
use oat\taoRevision\model\RepositoryService;
use oat\taoWorkspace\model\generis\WrapperModel;
use oat\taoWorkspace\model\lockStrategy\LockSystem;
use oat\taoWorkspace\model\lockStrategy\SqlStorage;
use oat\taoWorkspace\model\RevisionWrapper;

/**
 * @author Joel Bout <joel@taotesting.com>
 */
class SetupWrapper extends InstallAction
{
    /**
     * @param $params
     *
     * @throws common_Exception
     * @throws InvalidServiceManagerException
     */
    public function __invoke($params)
    {
        SqlStorage::createTable();

        $code = 666;
        $persistence = ModelManager::getModel()->getPersistence();

        /** @var common_persistence_Manager $pm */
        $pm = $this->getServiceManager()->get(PersistenceManager::SERVICE_ID);
        foreach ($pm->getOption(PersistenceManager::OPTION_PERSISTENCES) as $k => $p) {
            if ($persistence == $pm->getPersistenceById($k)){
                break;
            }
        }

        $workspaceModel = new SmoothModel(
            [
                SmoothModel::OPTION_PERSISTENCE => $k,
                SmoothModel::OPTION_READABLE_MODELS => [$code],
                SmoothModel::OPTION_WRITEABLE_MODELS => [$code],
                SmoothModel::OPTION_NEW_TRIPLE_MODEL => $code,
                SmoothModel::OPTION_SEARCH_SERVICE => ComplexSearchService::SERVICE_ID,
            ]
        );

        $model = ModelManager::getModel();
        $model->setOption(SmoothModel::OPTION_SEARCH_SERVICE, ComplexSearchService::SERVICE_ID);

        $wrappedModel = WrapperModel::wrap($model, $workspaceModel);
        $wrappedModel->setServiceLocator($this->getServiceLocator());
        ModelManager::setModel($wrappedModel);

        LockManager::setImplementation(new LockSystem());

        $oldRepository = $this->getServiceManager()->get(RepositoryInterface::SERVICE_ID);
        $this->registerService('taoWorkspace/innerRevision', $oldRepository);

        $newService = new RevisionWrapper(
            [
                RevisionWrapper::OPTION_INNER_IMPLEMENTATION => 'taoWorkspace/innerRevision',
                RepositoryService::OPTION_FILE_SYSTEM => 'revisions',
            ]
        );

        $this->registerService(RepositoryInterface::SERVICE_ID, $newService);
    }
}
