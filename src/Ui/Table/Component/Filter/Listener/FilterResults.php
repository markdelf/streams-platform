<?php namespace Anomaly\Streams\Platform\Ui\Table\Component\Filter\Listener;

use Anomaly\Streams\Platform\Ui\Table\Component\Filter\Contract\FilterInterface;
use Anomaly\Streams\Platform\Ui\Table\Component\Filter\FilterQuery;
use Anomaly\Streams\Platform\Ui\Table\Event\TableIsQuerying;

/**
 * Class FilterResults
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\Streams\Platform\Ui\Table\Listener
 */
class FilterResults
{

    /**
     * The filter query utility.
     *
     * @var \Anomaly\Streams\Platform\Ui\Table\Component\Filter\FilterQuery
     */
    protected $query;

    /**
     * Create a new FilterQueryHandler instance.
     *
     * @param FilterQuery $query
     */
    public function __construct(FilterQuery $query)
    {
        $this->query = $query;
    }

    /**
     * Handle the event.
     *
     * @param TableIsQuerying $event
     * @throws \Exception
     */
    public function handle(TableIsQuerying $event)
    {
        $query   = $event->getQuery();
        $builder = $event->getBuilder();
        $table   = $builder->getTable();

        $filters = $table->getFilters();

        foreach ($filters->active() as $filter) {
            if ($filter instanceof FilterInterface) {
                $this->query->filter($table, $query, $filter);
            }
        }
    }
}
