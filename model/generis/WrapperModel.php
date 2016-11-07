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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA
 *
 */
namespace oat\taoWorkspace\model\generis;

use oat\generis\model\data\Model;
use common_Logger;
use \common_exception_MissingParameter;
use \common_exception_Error;
use oat\generis\model\data\ModelManager;
use oat\oatbox\service\ConfigurableService;

/**
 * transitory model for the smooth sql implementation
 * 
 * @author joel bout <joel@taotesting.com>
 * @package generis
 */
class WrapperModel extends ConfigurableService
    implements Model
{
    
    static public function wrap(Model $original, Model $workspace ) {
        return new self(array('inner' => $original, 'workspace' => $workspace ));
    }
    
    /**
     * @var oat\generis\model\data\RdfInterface
     */
    private $rdf;
    
    /**
     * @var oat\generis\model\data\RdfsInterface
     */
    private $rdfs;
    
    function getResource($uri) {
        $resource = new \core_kernel_classes_Resource($uri);
        $resource->setModel($this);
        return $resource;
    }

    function getClass($uri) {
        $class = new \core_kernel_classes_Class($uri);
        $class->setModel($this);
        return $class;
    }

    function getProperty($uri) {
        $property = new \core_kernel_classes_Property($uri);
        $property->setModel($this);
        return $property;
    }
    
    /**
     * @return Model
     */
    public function getInnerModel()
    {
        return $this->getSubService('inner');
    }
    
    public function getWorkspaceModel()
    {
        return $this->getSubService('workspace');
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getRdfInterface()
     */
    public function getRdfInterface()
    {
        if (is_null($this->rdf)) {
            $this->rdf = new WrapperRdf($this->getInnerModel()->getRdfInterface(), $this);
        }
        return $this->rdf;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getRdfsInterface()
     */
    public function getRdfsInterface()
    {
        if (is_null($this->rdfs)) {
            $this->rdfs = new WrapperRdfs(
                $this->getInnerModel()->getRdfsInterface(),
                $this->getWorkspaceModel()->getRdfsInterface()
            );
        }
        return $this->rdfs;
    }
    
    public function getSearchInterface() {
        return $this->getInnerModel()->getSearchInterface();
    }

    public function getReadableModels()
    {
        return $this->getInnerModel()->getReadableModels();
    }

    public function addReadableModel($modelId)
    {
        common_Logger::i('Adding model '.$modelId.' via wrapper');
        $this->getInnerModel()->addReadableModel($modelId);
        
        // update in persistence
        ModelManager::setModel($this);
    }
}