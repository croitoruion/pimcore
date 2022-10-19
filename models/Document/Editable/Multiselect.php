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

namespace Pimcore\Model\Document\Editable;

use Pimcore\Model;

/**
 * @method \Pimcore\Model\Document\Editable\Dao getDao()
 */
class Multiselect extends Model\Document\Editable implements EditmodeDataInterface
{
    /**
     * Contains the current selected values
     *
     * @internal
     *
     * @var array
     */
    protected array $values = [];

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'multiselect';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->values;
    }

    public function getValues(): array
    {
        return $this->getData();
    }

    /**
     * {@inheritdoc}
     */
    public function frontend()
    {
        return implode(',', $this->values);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataEditmode(): array
    {
        return $this->values;
    }

    /**
     * {@inheritdoc}
     */
    public function setDataFromResource(mixed $data): Multiselect|EditableInterface|static
    {
        $this->values = \Pimcore\Tool\Serialize::unserialize($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDataFromEditmode(mixed $data): Multiselect|EditableInterface|static
    {
        if (empty($data)) {
            $this->values = [];
        } elseif (is_string($data)) {
            $this->values = explode(',', $data);
        } elseif (is_array($data)) {
            $this->values = $data;
        }

        return $this;
    }


    public function isEmpty(): bool
    {
        return empty($this->values);
    }
}
