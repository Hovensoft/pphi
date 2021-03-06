<?php
/**
 * Copyright Foacs
 * contributor(s): Alexis DINQUER
 *
 * (2019-05-08)
 *
 * contact@foacs.me
 *
 * This software is a computer program whose purpose is to handle data persistence in PHP
 *
 * This software is governed by the CeCILL-C license under french law and
 * abiding by the rules of distribution of free software. You can use,
 * modify and/ or redistribute the software under the terms of the CeCILL-C
 * license as circulated by CEA, CNRS and INRIA at the follow URL
 * "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and rights to copy,
 * modify and redistribute granted by the license, users are provided only
 * with a limited warranty and the software's authors, the holder of the
 * economic rights, and the successive licensors have only limited
 * liability.
 *
 * In this respect, the user's attention is drawn to the risk associated
 * with loading, using, modifying and/ or developing or reproducing the
 * software by the user in light of its specific status of free software,
 * that may mean that it is complicated to manipulate, and that also
 * therefore means that it is reserved for developers and experienced
 * professionals having in-depth computer knowledge. Users are therefore
 * encouraged to load and test the software's suitability as regards their
 * requirements in conditions enabling the security of their systems and/or
 * data to be ensure and, more generally, to use and operate it in the
 * same conditions as regards security.
 *
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL-C license and that you accept its terms.
 */
namespace PPHI;

use PPHI\Connector\ConnectionManager;
use PPHI\DataSource\DataSourceManager;
use PPHI\DataSource\Expert\MySQLExpert;
use PPHI\DataSource\Expert\Processor;
use PPHI\Exception\ConfigNotFoundException;
use PPHI\Exception\WrongFileFormatException;
use PPHI\utils\PPHILogger;
use PPHI\utils\ConfigFileLoader;

/*
 * ┌────┐┌────┐┌────┐┌────┐┌────┐
 * │ ┌──┘│ ┌┐ ││ ┌┐ ││ ┌──┘│ ┌──┘
 * │ └┐  │ ││ ││ └┘ ││ │   │ └──┐
 * │ ┌┘  │ ││ ││ ┌┐ ││ │   └──┐ │
 * │ │   │ └┘ ││ ││ ││ └──┐┌──┘ │
 * └─┘   └────┘└─┘└─┘└────┘└────┘
 */

/**
 * Main class for the PPHI project
 *
 * @package PPHI
 * @version 0.1.0
 * @api
 * @license CeCILL-C
 * @author Foacs
 */
class PPHI
{
    use ConfigFileLoader;
    const VERSION = "0.x.0";

    const DATA_SOURCES_PATH = "pphi/datasources";

    /**
     * @var array
     */
    private $dataSources = array();

    /**
     * @var DataSourceManager
     */
    private $dataSourcesManager;

    /**
     * @var ConnectionManager
     */
    private $connectionManager;

    private $logger;

    /**
     * PPHI constructor.
     * Load all dataSources found in {@link DATA_SOURCES_PATH} directory
     *
     * @throws ConfigNotFoundException when directory pphi/datasources is not found
     * @throws WrongFileFormatException when dataSources directory contains not YAML files
     * @throws Exception\UnknownDataSourcesTypeException when a data sources type is unknown
     */
    public function __construct()
    {
        $this->logger = PPHILogger::getLogger();

        $this->dataSourcesManager = new DataSourceManager(new Processor(), [new MySQLExpert()]);
        $this->connectionManager = new ConnectionManager();

        $this->dataSources = $this->getConfig(self::DATA_SOURCES_PATH);

        $this->logger->addInfo('Loading data sources ...', ['class' => 'PPHI']);
        $this->dataSourcesManager->load($this->dataSources);
        $this->connectionManager->addConnectionFromDataSourceArray($this->dataSourcesManager->getDataSources());

        $this->logger->addInfo('Data sources loaded', ['class' => 'PPHI']);
        echo "<pre>";
        print_r($this->connectionManager->getConnections());
        echo "<h1>Error</h1>";
        print_r($this->connectionManager->getAndFlushErrors());
        echo "</pre>";
    }
}
