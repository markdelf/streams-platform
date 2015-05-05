<?php namespace Anomaly\Streams\Platform\Ui\Table\Command;

use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Ui\Table\TableBuilder;
use Illuminate\Contracts\Bus\SelfHandling;

/**
 * Class SetTableStream
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Ui\Table\Command
 */
class SetTableStream implements SelfHandling
{

    /**
     * The table builder.
     *
     * @var TableBuilder
     */
    protected $builder;

    /**
     * Create a new SetTableStream instance.
     *
     * @param TableBuilder $builder
     */
    public function __construct(TableBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Handle the command.
     */
    public function handle()
    {
        $table = $this->builder->getTable();
        $model = $this->builder->getModel();

        /**
         * If the model is not set then they need
         * to load the table entries themselves.
         */
        if (!class_exists($model)) {
            return;
        }

        /*
         * Resolve the model
         * from the container.
         */
        $model = app($model);

        /**
         * If the model happens to be an instance of
         * EntryInterface then set the stream on the table.
         */
        if ($model instanceof EntryInterface) {
            $table->setStream($model->getStream());
        }
    }
}
