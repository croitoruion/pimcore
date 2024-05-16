<?php
declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\GenericExecutionEngineBundle\Extractor;

use Pimcore\Bundle\GenericExecutionEngineBundle\Entity\JobRun;
use Pimcore\Bundle\GenericExecutionEngineBundle\Messenger\Messages\GenericExecutionEngineMessageInterface;
use Pimcore\Bundle\GenericExecutionEngineBundle\Model\JobStepInterface;

interface JobRunExtractorInterface
{
    public function getJobRun(GenericExecutionEngineMessageInterface $message, bool $forceReload = false): JobRun;

    public function getJobStep(GenericExecutionEngineMessageInterface $message): JobStepInterface;

    public function getEnvironmentData(JobRun $jobRun): array;

    public function checkCondition(GenericExecutionEngineMessageInterface $message): bool;

    public function logMessageToJobRun(
        JobRun $jobRun,
        string $translationKey,
        array $params = []
    ): void;
}
