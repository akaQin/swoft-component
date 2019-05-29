<?php declare(strict_types=1);


namespace Swoft\Db\Eloquent;


use Closure;
use DateTimeInterface;
use Generator;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Db\Concern\BuildsQueries;
use Swoft\Db\Connection\Connection;
use Swoft\Db\Exception\DbException;
use Swoft\Db\Query\Builder as QueryBuilder;
use Swoft\Stdlib\Contract\Arrayable;
use Swoft\Stdlib\Helper\Arr;
use Swoft\Stdlib\Helper\PhpHelper;

/**
 * Class Builder
 *
 * @since 2.0
 * @method QueryBuilder select(string ...$columns)
 * @method QueryBuilder selectSub(Closure|QueryBuilder|string $query, string $as)
 * @method QueryBuilder selectRaw(string $expression, array $bindings = [])
 * @method QueryBuilder fromSub(Closure|QueryBuilder|string $query, string $as)
 * @method QueryBuilder fromRaw(string $expression, array $bindings = [])
 * @method QueryBuilder createSub(Closure|QueryBuilder|string $query)
 * @method QueryBuilder parseSub(Closure|QueryBuilder|string $query)
 * @method QueryBuilder addSelect(array $column)
 * @method QueryBuilder distinct()
 * @method QueryBuilder from(string $table)
 * @method QueryBuilder join(string $table, Closure|string $first, string $operator = null, string $second = null, string $type = 'inner', bool $where = false)
 * @method QueryBuilder joinWhere(string $table, Closure|string $first, string $operator, string $second, string $type = 'inner')
 * @method QueryBuilder joinSub(Closure|QueryBuilder|string $query, string $as, Closure|string $first, string $operator = null, string $second = null, string $type = 'inner', bool $where = false)
 * @method QueryBuilder leftJoin(string $table, Closure|string $first, string $operator = null, string $second = null)
 * @method QueryBuilder leftJoinWhere(string $table, string $first, string $operator, string $second)
 * @method QueryBuilder leftJoinSub(Closure|QueryBuilder|string $query, string $as, string $first, string $operator = null, string $second = null)
 * @method QueryBuilder rightJoin(string $table, Closure|string $first, string $operator = null, string $second = null)
 * @method QueryBuilder rightJoinWhere(string $table, string $first, string $operator, string $second)
 * @method QueryBuilder rightJoinSub(Closure|QueryBuilder|string $query, string $as, string $first, string $operator = null, string $second = null)
 * @method QueryBuilder crossJoin(string $table, Closure|string $first = null, string $operator = null, string $second = null)
 * @method void mergeWheres(array $wheres, array $bindings)
 * @method QueryBuilder whereColumn(string|array $first, string $operator = null, string $second = null, string $boolean = 'and')
 * @method QueryBuilder orWhereColumn(string|array $first, string $operator = null, string $second = null)
 * @method QueryBuilder whereRaw(string $sql, array $bindings = [], string $boolean = 'and')
 * @method QueryBuilder orWhereRaw(string $sql, array $bindings = [])
 * @method QueryBuilder whereIn(string $column, mixed $values, string $boolean = 'and', bool $not = false)
 * @method QueryBuilder orWhereIn(string $column, mixed $values)
 * @method QueryBuilder whereNotIn(string $column, mixed $values, string $boolean = 'and')
 * @method QueryBuilder orWhereNotIn(string $column, mixed $values)
 * @method QueryBuilder whereIntegerInRaw(string $column, Arrayable|array $values, string $boolean = 'and', bool $not = false)
 * @method QueryBuilder whereIntegerNotInRaw(string $column, Arrayable|array $values, string $boolean = 'and')
 * @method QueryBuilder whereNull(string $column, string $boolean = 'and', bool $not = false)
 * @method QueryBuilder orWhereNull(string $column)
 * @method QueryBuilder whereNotNull(string $column, string $boolean = 'and')
 * @method QueryBuilder whereBetween(string $column, array $values, string $boolean = 'and', bool $not = false)
 * @method QueryBuilder orWhereBetween(string $column, array $values)
 * @method QueryBuilder whereNotBetween(string $column, array $values, string $boolean = 'and')
 * @method QueryBuilder orWhereNotBetween(string $column, array $values)
 * @method QueryBuilder orWhereNotNull(string $column)
 * @method QueryBuilder whereDate(string $column, $operator, DateTimeInterface|string $value = null, string $boolean = 'and')
 * @method QueryBuilder orWhereDate(string $column, string $operator, DateTimeInterface|string $value = null)
 * @method QueryBuilder whereTime(string $column, $operator, DateTimeInterface|string $value = null, string $boolean = 'and')
 * @method QueryBuilder orWhereTime(string $column, string $operator, DateTimeInterface|string $value = null)
 * @method QueryBuilder whereDay(string $column, string $operator, DateTimeInterface|string $value = null, string $boolean = 'and')
 * @method QueryBuilder orWhereDay(string $column, string $operator, DateTimeInterface|string $value = null)
 * @method QueryBuilder whereMonth(string $column, string $operator, DateTimeInterface|string $value = null, string $boolean = 'and')
 * @method QueryBuilder orWhereMonth(string $column, string $operator, DateTimeInterface|string $value = null)
 * @method QueryBuilder whereYear(string $column, string $operator, DateTimeInterface|string|int $value = null, string $boolean = 'and')
 * @method QueryBuilder orWhereYear(string $column, string $operator, DateTimeInterface|string|int $value = null)
 * @method QueryBuilder whereNested(Closure $callback, string $boolean = 'and')
 * @method QueryBuilder forNestedWhere()
 * @method QueryBuilder addNestedWhereQuery(QueryBuilder $query, string $boolean = 'and')
 * @method QueryBuilder whereSub(string $column, string $operator, Closure $callback, string $boolean)
 * @method QueryBuilder whereExists(Closure $callback, string $boolean = 'and', bool $not = false)
 * @method QueryBuilder orWhereExists(Closure $callback, bool $not = false)
 * @method QueryBuilder whereNotExists(Closure $callback, string $boolean = 'and')
 * @method QueryBuilder orWhereNotExists(Closure $callback)
 * @method QueryBuilder addWhereExistsQuery(Builder $query, string $boolean = 'and', bool $not = false)
 * @method QueryBuilder whereRowValues(array $columns, string $operator, array $values, string $boolean = 'and')
 * @method QueryBuilder orWhereRowValues(array $columns, string $operator, array $values)
 * @method QueryBuilder whereJsonContains(string $column, $value, string $boolean = 'and', bool $not = false)
 * @method QueryBuilder orWhereJsonContains(string $column, mixed $value)
 * @method QueryBuilder whereJsonDoesntContain(string $column, mixed $value, string $boolean = 'and')
 * @method QueryBuilder orWhereJsonDoesntContain(string $column, mixed $value)
 * @method QueryBuilder whereJsonLength(string $column, mixed $operator, mixed $value = null, string $boolean = 'and')
 * @method QueryBuilder orWhereJsonLength(string $column, mixed $operator, mixed $value = null)
 * @method QueryBuilder groupBy(...$groups)
 * @method QueryBuilder having(string $column, string $operator = null, string $value = null, string $boolean = 'and')
 * @method QueryBuilder orHaving(string $column, string $operator = null, string $value = null)
 * @method QueryBuilder havingBetween(string $column, array $values, string $boolean = 'and', bool $not = false)
 * @method QueryBuilder havingRaw(string $sql, array $bindings = [], string $boolean = 'and')
 * @method QueryBuilder orHavingRaw(string $sql, array $bindings = [])
 * @method QueryBuilder orderBy(string $column, string $direction = 'asc')
 * @method QueryBuilder orderByDesc(string $column)
 * @method QueryBuilder inRandomOrder(string $seed = '')
 * @method QueryBuilder orderByRaw(string $sql, array $bindings = [])
 * @method QueryBuilder skip(int $value)
 * @method QueryBuilder offset(int $value)
 * @method QueryBuilder take(int $value)
 * @method QueryBuilder limit(int $value)
 * @method QueryBuilder forPage(int $page, int $perPage = 15)
 * @method QueryBuilder forPageAfterId(int $perPage = 15, int $lastId = null, string $column = 'id')
 * @method string insertGetId(array $values, string $sequence = null)
 * @method bool insert(array $values)
 * @method array getBindings()
 * @method string toSql()
 * @method bool exists()
 * @method bool doesntExist()
 * @method string count(string $columns = '*')
 * @method mixed min(string $column)
 * @method mixed max(string $column)
 * @method mixed sum(string $column)
 * @method mixed avg($column)
 * @method mixed average(string $column)
 * @method Connection getConnection()
 * @method string implode(string $column, string $glue = '')
 * @method array paginate(int $page = 1, int $perPage = 15, array $columns = ['*'])
 *
 */
class Builder
{
    use BuildsQueries;

    /**
     * The base query builder instance.
     *
     * @var QueryBuilder
     */
    protected $query;

    /**
     * The model being queried.
     *
     * @var Model
     */
    protected $model;

    /**
     * All of the globally registered builder macros.
     *
     * @var array
     */
    protected static $macros = [];

    /**
     * All of the locally registered builder macros.
     *
     * @var array
     */
    protected $localMacros = [];

    /**
     * A replacement for the typical delete function.
     *
     * @var Closure
     */
    protected $onDelete;

    /**
     * The methods that should be returned from query builder.
     *
     * @var array
     */
    protected $passthru = [
        'insert',
        'insertGetId',
        'getBindings',
        'toSql',
        'exists',
        'doesntExist',
        'count',
        'min',
        'max',
        'avg',
        'average',
        'sum',
        'implode',
        'pluck',
        'getConnection',
        'updateOrInsert',
        'paginate'
    ];

    /**
     * Create a new Eloquent query builder instance.
     *
     * @param QueryBuilder $query
     *
     * @return void
     */
    public function __construct(QueryBuilder $query)
    {
        $this->query = $query;
    }

    /**
     * Create and return an un-saved model instance.
     *
     * @param array $attributes
     *
     * @return Model
     * @throws DbException
     */
    public function make(array $attributes = [])
    {
        return $this->newModelInstance($attributes);
    }

    /**
     * Add a where clause on the primary key to the query.
     *
     * @param $id
     *
     * @return $this|Builder
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function whereKey($id)
    {
        if (is_array($id) || $id instanceof Arrayable) {
            $this->query->whereIn($this->model->getQualifiedKeyName(), $id);

            return $this;
        }

        return $this->where($this->model->getQualifiedKeyName(), '=', $id);
    }

    /**
     * @param $id
     *
     * @return $this|Builder
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function whereKeyNot($id)
    {
        if (is_array($id) || $id instanceof Arrayable) {
            $this->query->whereNotIn($this->model->getQualifiedKeyName(), $id);

            return $this;
        }

        return $this->where($this->model->getQualifiedKeyName(), '!=', $id);
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param string|array|Closure $column
     * @param mixed                $operator
     * @param mixed                $value
     * @param string               $boolean
     *
     * @return $this
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function where($column, $operator = null, $value = null, string $boolean = 'and')
    {
        if ($column instanceof Closure) {
            $column($query = $this->model->newModelQuery());

            $this->query->addNestedWhereQuery($query->getQuery(), $boolean);
        } else {
            $this->query->where(...func_get_args());
        }

        return $this;
    }

    /**
     * Add an "or where" clause to the query.
     *
     * @param Closure|array|string $column
     * @param mixed                $operator
     * @param mixed                $value
     *
     * @return Builder
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        [$value, $operator] = $this->query->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->where($column, $operator, $value, 'or');
    }

    /**
     * Add an "order by" clause for a timestamp to the query.
     *
     * @param string $column
     *
     * @return $this
     */
    public function latest(string $column = null)
    {
        $this->query->latest($column);

        return $this;
    }

    /**
     * Add an "order by" clause for a timestamp to the query.
     *
     * @param string $column
     *
     * @return $this
     */
    public function oldest(string $column = null)
    {
        $this->query->oldest($column);

        return $this;
    }

    /**
     * Create a collection of models from plain arrays.
     *
     * @param array $items
     *
     * @return Collection
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function hydrate(array $items): Collection
    {
        $instance = $this->newModelInstance();

        return $instance->newCollection(array_map(function ($item) use ($instance) {
            return $instance->newFromBuilder($item);
        }, $items));
    }

    /**
     * Create a collection of models from a raw query.
     *
     * @param string $query
     * @param array  $bindings
     *
     * @return Collection
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function fromQuery(string $query, array $bindings = [])
    {
        return $this->hydrate(
            $this->query->getConnection()->select($query, $bindings)
        );
    }

    /**
     * Find a model by its primary key.
     *
     * @param mixed $id
     * @param array $columns
     *
     * @return null|object|Builder|Collection|Model
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function find($id, array $columns = ['*'])
    {
        if (is_array($id) || $id instanceof Arrayable) {
            return $this->findMany($id, $columns);
        }

        return $this->whereKey($id)->first($columns);
    }

    /**
     * Find multiple models by their primary keys.
     *
     * @param Arrayable|array $ids
     * @param array           $columns
     *
     * @return Collection
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function findMany(array $ids, array $columns = ['*'])
    {
        if (empty($ids)) {
            return $this->model->newCollection();
        }

        return $this->whereKey($ids)->get($columns);
    }

    /**
     * Find a model by its primary key or throw an exception.
     *
     * @param mixed $id
     * @param array $columns
     *
     * @return null|object|Builder|Collection|Model
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function findOrFail($id, array $columns = ['*'])
    {
        $result = $this->find($id, $columns);

        if (is_array($id)) {
            if (count($result) === count(array_unique($id))) {
                return $result;
            }
        } elseif (!is_null($result)) {
            return $result;
        }

        throw new DbException('Model is not finded');
    }

    /**
     * Find a model by its primary key or return fresh model instance.
     *
     * @param mixed $id
     * @param array $columns
     *
     * @return null|object|Builder|Collection|Model
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function findOrNew($id, array $columns = ['*'])
    {
        if (!is_null($model = $this->find($id, $columns))) {
            return $model;
        }

        return $this->newModelInstance();
    }

    /**
     * Get the first record matching the attributes or instantiate it.
     *
     * @param array $attributes
     * @param array $values
     *
     * @return null|object|Builder|Model
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function firstOrNew(array $attributes, array $values = [])
    {
        if (!is_null($instance = $this->where($attributes)->first())) {
            return $instance;
        }

        return $this->newModelInstance($attributes + $values);
    }

    /**
     * Get the first record matching the attributes or create it.
     *
     * @param array $attributes
     * @param array $values
     *
     * @return null|object|Builder|Model
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function firstOrCreate(array $attributes, array $values = [])
    {
        if (!is_null($instance = $this->where($attributes)->first())) {
            return $instance;
        }

        $instance = $this->newModelInstance($attributes + $values);
        $instance->save();

        return $instance;
    }

    /**
     * Create or update a record matching the attributes, and fill it with values.
     *
     * @param array $attributes
     * @param array $values
     *
     * @return null|object|Builder|Model
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        $instance = $this->firstOrNew($attributes);
        $instance->fill($values)->save();
        return $instance;
    }

    /**
     * Execute the query and get the first result or throw an exception.
     *
     * @param array $columns
     *
     * @return Model|static
     *
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function firstOrFail(array $columns = ['*'])
    {
        if (!is_null($model = $this->first($columns))) {
            return $model;
        }

        throw new DbException('Model is not find');
    }

    /**
     * Execute the query and get the first result or call a callback.
     *
     * @param Closure|array $columns
     * @param Closure|null  $callback
     *
     * @return Model|static|mixed
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function firstOr(array $columns = ['*'], Closure $callback = null)
    {
        if ($columns instanceof Closure) {
            $callback = $columns;

            $columns = ['*'];
        }

        if (!is_null($model = $this->first($columns))) {
            return $model;
        }

        return call_user_func($callback);
    }

    /**
     * Get a single column's value from the first result of a query.
     *
     * @param string $column
     *
     * @return mixed
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function value(string $column)
    {
        if ($result = $this->first([$column])) {
            return $result->getAttributeValue($column);
        }

        return null;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param array $columns
     *
     * @return Collection
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function get(array $columns = ['*'])
    {
        $builder = $this;

        $models = $builder->getModels($columns);
        return $builder->getModel()->newCollection($models);
    }

    /**
     * Get the hydrated models without eager loading.
     *
     * @param array $columns
     *
     * @return Model[]|static[]
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function getModels(array $columns = ['*'])
    {
        return $this->hydrate(
            $this->query->get($columns)->all()
        )->all();
    }

    /**
     * Get a generator for the given query.
     *
     * @return Generator
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function cursor()
    {
        foreach ($this->query->cursor() as $record) {
            yield $this->model->newFromBuilder($record);
        }
    }

    /**
     * Chunk the results of a query by comparing numeric IDs.
     *
     * @param int         $count
     * @param callable    $callback
     * @param string|null $column
     * @param string|null $alias
     *
     * @return bool
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function chunkById(int $count, callable $callback, string $column = null, string $alias = null): bool
    {
        $column = is_null($column) ? $this->getModel()->getKeyName() : $column;

        $alias = is_null($alias) ? $column : $alias;

        $lastId = null;

        do {
            $clone = clone $this;

            // We'll execute the query for the given page and get the results. If there are
            // no results we can just break and return from here. When there are results
            // we will call the callback with the current chunk of these results here.
            $results = $clone->forPageAfterId($count, $lastId, $column)->get();

            $countResults = $results->count();

            if ($countResults == 0) {
                break;
            }

            // On each chunk result set, we will pass them to the callback and then let the
            // developer take care of everything within the callback, which allows us to
            // keep the memory low for spinning through large result sets for working.
            if ($callback($results) === false) {
                return false;
            }

            /* @var Model $last */
            $last   = $results->last();
            $lastId = $last->getAttributeValue($alias);

            unset($results);
        } while ($countResults == $count);

        return true;
    }

    /**
     * Add a generic "order by" clause if the query doesn't already have one.
     *
     * @return void
     * @throws DbException
     */
    protected function enforceOrderBy()
    {
        if (empty($this->query->orders) && empty($this->query->unionOrders)) {
            $this->orderBy($this->model->getQualifiedKeyName(), 'asc');
        }
    }

    /**
     * Save a new model and return the instance.
     *
     * @param array $attributes
     *
     * @return Model
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function create(array $attributes = [])
    {
        $instance = $this->newModelInstance($attributes);
        $instance->save();
        return $instance;
    }

    /**
     * Update a record in the database.
     *
     * @param array $values
     *
     * @return int
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function update(array $values)
    {
        return $this->toBase()->update($values);
    }

    /**
     * Increment a column's value by a given amount.
     *
     * @param string    $column
     * @param float|int $amount
     * @param array     $extra
     *
     * @return int
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function increment(string $column, $amount = 1, array $extra = [])
    {
        return $this->toBase()->increment(
            $column, $amount, $extra
        );
    }

    /**
     * Decrement a column's value by a given amount.
     *
     * @param string    $column
     * @param float|int $amount
     * @param array     $extra
     *
     * @return int
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function decrement($column, $amount = 1, array $extra = [])
    {
        return $this->toBase()->decrement(
            $column, $amount, $extra
        );
    }

    /**
     * Delete a record from the database.
     *
     * @return int|mixed
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function delete()
    {
        if (isset($this->onDelete)) {
            return call_user_func($this->onDelete, $this);
        }

        return $this->toBase()->delete();
    }

    /**
     * Run the default delete function on the builder.
     *
     * Since we do not apply scopes here, the row will actually be deleted.
     *
     * @return int
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function forceDelete()
    {
        return $this->query->delete();
    }

    /**
     * Register a replacement for the default delete function.
     *
     * @param Closure $callback
     *
     * @return void
     */
    public function onDelete(Closure $callback)
    {
        $this->onDelete = $callback;
    }

    /**
     * Create a new instance of the model being queried.
     *
     * @param array $attributes
     *
     * @return Model
     * @throws DbException
     */
    public function newModelInstance($attributes = [])
    {
        return $this->model->newInstance($attributes);
    }

    /**
     * Get the underlying query builder instance.
     *
     * @return QueryBuilder
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set the underlying query builder instance.
     *
     * @param QueryBuilder $query
     *
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get a base query builder instance.
     *
     * @return QueryBuilder
     */
    public function toBase()
    {
        return $this->getQuery();
    }

    /**
     * Get the model instance being queried.
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set a model instance for the model being queried.
     *
     * @param Model $model
     *
     * @return $this
     * @throws DbException
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        $this->query->from($model->getTable());

        return $this;
    }

    /**
     * Qualify the given column name by the model's table.
     *
     * @param string $column
     *
     * @return string
     * @throws DbException
     */
    public function qualifyColumn($column)
    {
        return $this->model->qualifyColumn($column);
    }

    /**
     * Get the given macro by name.
     *
     * @param string $name
     *
     * @return Closure
     */
    public function getMacro($name)
    {
        return Arr::get($this->localMacros, $name);
    }

    /**
     * Dynamically handle calls into the query instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, $this->passthru)) {
            return $this->toBase()->{$method}(...$parameters);
        }

        PhpHelper::call([$this->query, $method], ...$parameters);

        return $this;
    }

    /**
     * Force a clone of the underlying query builder when cloning.
     *
     * @return void
     */
    public function __clone()
    {
        $this->query = clone $this->query;
    }
}
