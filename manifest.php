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
use oat\taoWorkspace\scripts\uninstall\UninstallWorkspace;
use oat\taoWorkspace\scripts\install\SetupWrapper;

return array(
    'name' => 'taoWorkspace',
    'label' => 'Workspace',
    'description' => 'Supports workspaces for items',
    'license' => 'GPL-2.0',
    'version' => '1.1.0',
    'author' => 'Open Assessment Technologies SA',
    'requires' => array(
        'generis' => '>=4.4.2',
        'taoItems' => '>=2.6.3',
        'taoRevision' => '>=2.2.0'
    ),
    'managementRole' => 'http://www.tao.lu/Ontologies/generis.rdf#taoWorkspaceManager',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#taoWorkspaceManager', array('ext'=>'taoWorkspace')),
    ),
    'install' => array(
        'php' => array(
            SetupWrapper::class
        )
    ),
    'uninstall' => array(
        'php' => array(
            UninstallWorkspace::class
        )
    ),
    'update' => 'oat\\taoWorkspace\\scripts\\update\\Updater',
    'constants' => array(
        # views directory
        "DIR_VIEWS" => dirname(__FILE__).DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR,
        
        #BASE URL (usually the domain root)
        'BASE_URL' => ROOT_URL.'taoWorkspace/',
    ),
    'extra' => array(
        'structures' => dirname(__FILE__).DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'structures.xml',
    )
);
