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

namespace PPHI\DataSource\Expert;

use PPHI\DataSource\Source\DataSource;

/**
 * Class Processor
 * Use to process a data source type, will use expert.
 *
 * @package PPHI\DataSource\Expert
 * @version 0.1.0
 * @api
 * @license CeCILL-C
 * @author Foacs
 */
class Processor
{

    /**
     * @var Expert
     */
    private $expert;

    /**
     * Try all expert in the chain and give the found result or null.
     *
     * @param string $str The string which will be processed
     *
     * @return DataSource|null The found data source
     */
    public function execute(string $str): ?DataSource
    {
        if (!is_null($this->expert)) {
            return $this->expert->execute($str);
        }
        return null;
    }

    /**
     * Add an expert to the chain.
     *
     * @param Expert $expert The epxert to add.
     */
    public function pushExpert(Expert $expert): void
    {
        $tmp = $this->expert;
        $expert->setNext($tmp);
        $this->expert = $expert;
    }

    /**
     * Get the head of the chain.
     *
     * @return Expert The head of the chain.
     */
    public function getExpert(): ?Expert
    {
        return $this->expert;
    }
}
