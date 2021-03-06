<?php
/**
 * Copyright Foacs
 * contributor(s): Alexis DINQUER
 *
 * (2019-05-09)
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
namespace PPHI\DataSource;

use Monolog\Logger;
use PPHI\DataSource\Expert\Processor;
use PPHI\DataSource\Source\DataSource;
use PPHI\Exception\UnknownDataSourcesTypeException;
use PPHI\utils\PPHILogger;

/**
 * Class DataSourceManager
 * Handle data source.
 *
 * @package PPHI\DataSource
 * @version 0.1.0
 * @api
 * @license CeCILL-C
 * @author Foacs
 */
class DataSourceManager
{
    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var array
     */
    private $dataSources = [];

    /**
     * @var Logger
     */
    private $logger;

    /**
     * DataSourceManager constructor.
     *
     * @param Processor $processor The expert processor
     * @param array $experts The experts used to resolve the datatype
     */
    public function __construct(Processor $processor, array $experts)
    {
        $this->logger = PPHILogger::getLogger();
        $this->processor = $processor;
        foreach ($experts as $expert) {
            $this->processor->pushExpert($expert);
        }
    }

    /**
     * Load all data sources from config directory in $dataSources;
     *
     * @param array $dataSources Contains all data sources configuration
     * @return int Number of loaded dataSource
     * @throws UnknownDataSourcesTypeException when found an unknown data sources type
     */
    public function load(array $dataSources): int
    {
        $this->logger->addInfo('Loading data sources ...', ['class' => 'DataSourceManager']);
        $res = 0;
        foreach ($dataSources as $dataSourceName => $dataSource) {
            $this->logger->addDebug('Load data source ' . $dataSourceName, ['class' => 'DataSourceManager']);
            $dataSourceType = strtolower($dataSource['type']) ?? "mysql";
            $ds = $this->processor->execute($dataSourceType);
            if (is_null($ds)) {
                throw new UnknownDataSourcesTypeException("The data sources type " . $dataSourceType . " is unknown");
            }
            $ds->setUp($dataSource);
            $this->dataSources[$dataSourceName] = $ds;
            $res++;
        }
        $this->logger->addInfo('Load ' . $res . ' data sources', ['class' => 'DataSourceManager']);
        return $res;
    }

    /**
     * Get all loaded data sources
     *
     * @return DataSource[] An array of DataSource
     */
    public function getDataSources(): array
    {
        return $this->dataSources;
    }
}
