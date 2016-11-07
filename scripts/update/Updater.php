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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */
namespace oat\taoWorkspace\scripts\update;

use common_ext_Extension;
use common_ext_ExtensionsManager;
use common_ext_ExtensionUpdater;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\taoRevision\model\Repository;
use oat\taoRevision\model\RepositoryService;
use oat\taoWorkspace\model\RevisionWrapper;
/**
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class Updater extends common_ext_ExtensionUpdater
{

    /**
     *
     * @param string $initialVersion
     * @return void
     */
    public function update($initialVersion)
    {
        if ($this->isBetween('0', '0.2')){
            $this->setVersion('0.2');
        }
        
        if ($this->isVersion('0.2')) { 
            $oldRepository = $this->getServiceManager()->get(Repository::SERVICE_ID);
            $this->getServiceManager()->register('taoWorkspace/innerRevision', $oldRepository);
            
            $newService = new RevisionWrapper(array(RevisionWrapper::OPTION_INNER_IMPLEMENTATION => 'taoWorkspace/innerRevision'));
            $this->getServiceManager()->register(Repository::SERVICE_ID, $newService);
            $this->setVersion('0.3.0');
        }

        $this->skip('0.3.0', '0.4.1');

        if ($this->isVersion('0.4.1')) {

            $service = $this->getServiceManager()->get(Repository::SERVICE_ID);
            $service->setOption(RepositoryService::OPTION_FS, 'revisions');
            $this->getServiceManager()->register(Repository::SERVICE_ID, $service);

            $this->setVersion('0.5.0');
        }
        
        $this->skip('0.5.0', '0.6.0');
        
        if ($this->isVersion('0.6.0')) {
            /* @var $extension common_ext_Extension */
            $extension = common_ext_ExtensionsManager::singleton()->getExtensionById('generis');
            $config = $extension->getConfig('ontology');
            
            $config['inner']['search']     = ComplexSearchService::SERVICE_ID ;
            $config['workspace']['search'] = ComplexSearchService::SERVICE_ID ;
            
            $extension->setConfig('ontology', $config);
            $this->setVersion('0.6.1');
        }
    }
}
