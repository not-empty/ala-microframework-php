<?php

namespace App\Repositories;

use App\Constants\FiltersTypesConstants;
use DatabaseCache\Repository;
use Illuminate\Database\DatabaseManager;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ulid\Ulid;

class BaseRepositoryCacheTest extends TestCase
{
    /**
     * @covers \App\Repositories\BaseRepositoryCache::__construct
     */
    public function testCreateBaseRepositoryCache()
    {
        $dbSpy = Mockery::spy(DatabaseManager::class);
        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = $this->getMockForAbstractClass(
            BaseRepositoryCache::class,
            [
                $dbSpy,
                $ulidSpy
            ]
        );
        $this->assertInstanceOf(
            BaseRepositoryCache::class,
            $baseRepositoryCache
        );
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::getById
     */
    public function testGetById()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $repositoryMock = Mockery::mock(Repository::class)
            ->shouldReceive('getQuery')
            ->with(':1')
            ->once()
            ->andReturn(null)
            ->shouldReceive('setQuery')
            ->with(':1', json_encode($return))
            ->once()
            ->andReturn(true)
            ->getMock();

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->with(null)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('find')
            ->with(1)
            ->once()
            ->andReturn($return)
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('newCacheRepository')
            ->withNoArgs()
            ->once()
            ->andReturn($repositoryMock);

        $getById = $baseRepositoryCache->getById(1);

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::getById
     */
    public function testGetByIdWithoutDataAndNotMakeCache()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $repositoryMock = Mockery::mock(Repository::class)
            ->shouldReceive('getQuery')
            ->with(':1')
            ->once()
            ->andReturn(null)
            ->shouldReceive('setQuery')
            ->never()
            ->andReturn(true)
            ->getMock();

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->with(null)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('find')
            ->with(1)
            ->once()
            ->andReturn([])
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('newCacheRepository')
            ->withNoArgs()
            ->once()
            ->andReturn($repositoryMock);

        $getById = $baseRepositoryCache->getById(1);

        $this->assertEquals([], $getById);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::getById
     */
    public function testGetByIdAndReturnCache()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $repositoryMock = Mockery::mock(Repository::class)
            ->shouldReceive('getQuery')
            ->with(':1')
            ->once()
            ->andReturn(json_encode($return))
            ->shouldReceive('setQuery')
            ->never()
            ->andReturn(true)
            ->getMock();

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('find')
            ->never()
            ->andReturn($return)
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('newCacheRepository')
            ->withNoArgs()
            ->once()
            ->andReturn($repositoryMock);

        $getById = $baseRepositoryCache->getById(1);

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::getDeadById
     */
    public function testGetDeadById()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $repositoryMock = Mockery::mock(Repository::class)
            ->shouldReceive('getQuery')
            ->with(':1')
            ->once()
            ->andReturn(null)
            ->shouldReceive('setQuery')
            ->with(':1', json_encode($return))
            ->once()
            ->andReturn(true)
            ->getMock();

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->with(null)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('whereNotNull')
            ->with('deleted')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($return)
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('newCacheRepository')
            ->withNoArgs()
            ->once()
            ->andReturn($repositoryMock);

        $getById = $baseRepositoryCache->getDeadById(1);

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::getDeadById
     */
    public function testGetDeadByIdAndReturnCache()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $repositoryMock = Mockery::mock(Repository::class)
            ->shouldReceive('getQuery')
            ->with(':1')
            ->once()
            ->andReturn(json_encode($return))
            ->shouldReceive('setQuery')
            ->never()
            ->andReturn(true)
            ->getMock();

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('whereNotNull')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('find')
            ->never()
            ->andReturn($return)
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('newCacheRepository')
            ->withNoArgs()
            ->once()
            ->andReturn($repositoryMock);

        $getById = $baseRepositoryCache->getDeadById(1);

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::getDeadById
     */
    public function testGetDeadByIdWithoutDataAndNotMakeCache()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $repositoryMock = Mockery::mock(Repository::class)
            ->shouldReceive('getQuery')
            ->with(':1')
            ->once()
            ->andReturn(null)
            ->shouldReceive('setQuery')
            ->never()
            ->andReturn(true)
            ->getMock();

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->with(null)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('whereNotNull')
            ->with('deleted')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('find')
            ->with(1)
            ->once()
            ->andReturn([])
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('newCacheRepository')
            ->withNoArgs()
            ->once()
            ->andReturn($repositoryMock);

        $getById = $baseRepositoryCache->getDeadById(1);

        $this->assertEquals([], $getById);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::getList
     */
    public function testGetListPaginated()
    {
        $return = [
            'data' => [
                'id' => 'id',
                'name' => 'teste',
            ],
        ];

        $query = [
            'page' => 2,
        ];

        $repositoryMock = Mockery::mock(Repository::class)
            ->shouldReceive('generateIdentifierByArray')
            ->with($query)
            ->once()
            ->andReturn(':')
            ->shouldReceive('getQuery')
            ->with(':2')
            ->once()
            ->andReturn(null)
            ->shouldReceive('setQuery')
            ->with(':2', json_encode($return))
            ->once()
            ->andReturn(true)
            ->getMock();

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->with(null)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('id', 'desc')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25, ['*'], 'page', 2)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->once()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->with($query)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('links')
            ->withNoArgs()
            ->once()
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('newCacheRepository')
            ->withNoArgs()
            ->once()
            ->andReturn($repositoryMock)
            ->shouldReceive('setWheres')
            ->with($dbMock, ['whereNull' => 'deleted'])
            ->once()
            ->andReturn($dbMock)
            ->shouldReceive('setFilters')
            ->with($dbMock, null)
            ->once()
            ->andReturn($dbMock);

        $getList = $baseRepositoryCache->getList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            null,
            $query
        );

        $this->assertEquals($return, $getList);
        $this->assertEquals($return['data']['id'], 'id');
        $this->assertEquals($return['data']['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::setFilters
     */
    public function testGetList()
    {
        $return = [
            'data' => [
                'id' => 'id',
                'name' => 'teste',
            ],
        ];

        $query = [];

        $repositoryMock = Mockery::mock(Repository::class)
            ->shouldReceive('generateIdentifierByArray')
            ->with($query)
            ->once()
            ->andReturn(':')
            ->shouldReceive('getQuery')
            ->with(':1')
            ->once()
            ->andReturn(null)
            ->shouldReceive('setQuery')
            ->with(':1', json_encode($return))
            ->once()
            ->andReturn(true)
            ->getMock();

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->with(null)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('id', 'desc')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25, ['*'], 'page', 1)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->once()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->with($query)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('links')
            ->withNoArgs()
            ->once()
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('newCacheRepository')
            ->withNoArgs()
            ->once()
            ->andReturn($repositoryMock)
            ->shouldReceive('setWheres')
            ->with($dbMock, ['whereNull' => 'deleted'])
            ->once()
            ->andReturn($dbMock)
            ->shouldReceive('setFilters')
            ->with($dbMock, null)
            ->once()
            ->andReturn($dbMock);

        $getList = $baseRepositoryCache->getList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            null,
            $query
        );

        $this->assertEquals($return, $getList);
        $this->assertEquals($return['data']['id'], 'id');
        $this->assertEquals($return['data']['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::getList
     */
    public function testGetListAndReturnCache()
    {
        $return = [
            'data' => [
                'id' => 'id',
                'name' => 'teste',
            ],
        ];

        $query = [];

        $repositoryMock = Mockery::mock(Repository::class)
            ->shouldReceive('generateIdentifierByArray')
            ->with($query)
            ->once()
            ->andReturn(':')
            ->shouldReceive('getQuery')
            ->with(':1')
            ->once()
            ->andReturn(json_encode($return))
            ->shouldReceive('setQuery')
            ->never()
            ->andReturn(true)
            ->getMock();

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('select')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->never()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('links')
            ->never()
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('newCacheRepository')
            ->withNoArgs()
            ->once()
            ->andReturn($repositoryMock)
            ->shouldReceive('setWheres')
            ->never()
            ->andReturn($dbMock)
            ->shouldReceive('setFilters')
            ->never()
            ->andReturn($dbMock);

        $getList = $baseRepositoryCache->getList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            null,
            $query
        );

        $this->assertEquals($return, $getList);
        $this->assertEquals($return['data']['id'], 'id');
        $this->assertEquals($return['data']['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::getList
     */
    public function testGetListWithoutDataAndNotCache()
    {
        $return = [
            'data' => [
            ],
        ];

        $query = [];

        $repositoryMock = Mockery::mock(Repository::class)
            ->shouldReceive('generateIdentifierByArray')
            ->with($query)
            ->once()
            ->andReturn(':')
            ->shouldReceive('getQuery')
            ->with(':1')
            ->once()
            ->andReturn(null)
            ->shouldReceive('setQuery')
            ->never()
            ->andReturn(true)
            ->getMock();

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->with(null)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('id', 'desc')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25, ['*'], 'page', 1)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->once()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->with($query)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('links')
            ->withNoArgs()
            ->once()
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('newCacheRepository')
            ->withNoArgs()
            ->once()
            ->andReturn($repositoryMock)
            ->shouldReceive('setWheres')
            ->with($dbMock, ['whereNull' => 'deleted'])
            ->once()
            ->andReturn($dbMock)
            ->shouldReceive('setFilters')
            ->with($dbMock, null)
            ->once()
            ->andReturn($dbMock);

        $getList = $baseRepositoryCache->getList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            null,
            $query
        );

        $this->assertEquals($return, $getList);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::setWheres
     */
    public function testSetWheres()
    {
        $where = [
            'whereNull' => 'deleted'
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->once()
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = $this->getMockForAbstractClass(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $setWheres = $baseRepositoryCache->setWheres($dbMock, $where);

        $this->assertInstanceOf(DatabaseManager::class, $setWheres);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::getDeadList
     */
    public function testGetDeadListPaginated()
    {
        $return = [
            'data' => [
                'id' => 'id',
                'name' => 'teste',
            ],
        ];

        $query = [
            'page' => 2,
        ];

        $repositoryMock = Mockery::mock(Repository::class)
            ->shouldReceive('generateIdentifierByArray')
            ->with($query)
            ->once()
            ->andReturn(':')
            ->shouldReceive('getQuery')
            ->with(':2')
            ->once()
            ->andReturn(null)
            ->shouldReceive('setQuery')
            ->with(':2', json_encode($return))
            ->once()
            ->andReturn(true)
            ->getMock();

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->with(null)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('id', 'desc')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25, ['*'], 'page', 2)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->once()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->with($query)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('links')
            ->withNoArgs()
            ->once()
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('newCacheRepository')
            ->withNoArgs()
            ->once()
            ->andReturn($repositoryMock)
            ->shouldReceive('setWheres')
            ->with($dbMock, ['whereNotNull' => 'deleted'])
            ->once()
            ->andReturn($dbMock)
            ->shouldReceive('setFilters')
            ->with($dbMock, null)
            ->once()
            ->andReturn($dbMock);

        $getDeadList = $baseRepositoryCache->getDeadList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            null,
            $query
        );

        $this->assertEquals($return, $getDeadList);
        $this->assertEquals($return['data']['id'], 'id');
        $this->assertEquals($return['data']['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::getDeadList
     */
    public function testGetDeadList()
    {
        $return = [
            'data' => [
                'id' => 'id',
                'name' => 'teste',
            ],
        ];

        $query = [];

        $repositoryMock = Mockery::mock(Repository::class)
            ->shouldReceive('generateIdentifierByArray')
            ->with($query)
            ->once()
            ->andReturn(':')
            ->shouldReceive('getQuery')
            ->with(':1')
            ->once()
            ->andReturn(null)
            ->shouldReceive('setQuery')
            ->with(':1', json_encode($return))
            ->once()
            ->andReturn(true)
            ->getMock();

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->with(null)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('id', 'desc')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25, ['*'], 'page', 1)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->once()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->with($query)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('links')
            ->withNoArgs()
            ->once()
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('newCacheRepository')
            ->withNoArgs()
            ->once()
            ->andReturn($repositoryMock)
            ->shouldReceive('setWheres')
            ->with($dbMock, ['whereNotNull' => 'deleted'])
            ->once()
            ->andReturn($dbMock)
            ->shouldReceive('setFilters')
            ->with($dbMock, null)
            ->once()
            ->andReturn($dbMock);

        $getDeadList = $baseRepositoryCache->getDeadList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            null,
            $query
        );

        $this->assertEquals($return, $getDeadList);
        $this->assertEquals($return['data']['id'], 'id');
        $this->assertEquals($return['data']['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::getDeadList
     */
    public function testGetDeadListAndReturnCache()
    {
        $return = [
            'data' => [
                'id' => 'id',
                'name' => 'teste',
            ],
        ];

        $query = [];

        $repositoryMock = Mockery::mock(Repository::class)
            ->shouldReceive('generateIdentifierByArray')
            ->with($query)
            ->once()
            ->andReturn(':')
            ->shouldReceive('getQuery')
            ->with(':1')
            ->once()
            ->andReturn(json_encode($return))
            ->shouldReceive('setQuery')
            ->never()
            ->andReturn(true)
            ->getMock();

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('select')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->never()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('links')
            ->never()
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('newCacheRepository')
            ->withNoArgs()
            ->once()
            ->andReturn($repositoryMock)
            ->shouldReceive('setWheres')
            ->never()
            ->andReturn($dbMock)
            ->shouldReceive('setFilters')
            ->never()
            ->andReturn($dbMock);

        $getDeadList = $baseRepositoryCache->getDeadList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            null,
            $query
        );

        $this->assertEquals($return, $getDeadList);
        $this->assertEquals($return['data']['id'], 'id');
        $this->assertEquals($return['data']['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::getDeadList
     */
    public function testGetDeadListWithoutDataAndNotCache()
    {
        $return = [
            'data' => [
            ],
        ];

        $query = [];

        $repositoryMock = Mockery::mock(Repository::class)
            ->shouldReceive('generateIdentifierByArray')
            ->with($query)
            ->once()
            ->andReturn(':')
            ->shouldReceive('getQuery')
            ->with(':1')
            ->once()
            ->andReturn(null)
            ->shouldReceive('setQuery')
            ->never()
            ->andReturn(true)
            ->getMock();

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->with(null)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('id', 'desc')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25, ['*'], 'page', 1)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->once()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->with($query)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('links')
            ->withNoArgs()
            ->once()
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('newCacheRepository')
            ->withNoArgs()
            ->once()
            ->andReturn($repositoryMock)
            ->shouldReceive('setWheres')
            ->with($dbMock, ['whereNotNull' => 'deleted'])
            ->once()
            ->andReturn($dbMock)
            ->shouldReceive('setFilters')
            ->with($dbMock, null)
            ->once()
            ->andReturn($dbMock);

        $getDeadList = $baseRepositoryCache->getDeadList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            null,
            $query
        );

        $this->assertEquals($return, $getDeadList);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::getBulk
     */
    public function testGetBulk()
    {
        $return = [
            'data' => [
                'id' => 'id',
                'name' => 'teste',
            ],
        ];

        $ids = [
            1,
            2,
            '*' => [
                1,
                2,
            ],
        ];

        $repositoryMock = Mockery::mock(Repository::class)
            ->shouldReceive('generateIdentifierByArray')
            ->with($ids['*'])
            ->once()
            ->andReturn(':1:2:')
            ->shouldReceive('getQuery')
            ->with(':bulk:1:2:')
            ->once()
            ->andReturn(null)
            ->shouldReceive('setQuery')
            ->with(':bulk:1:2:', json_encode($return))
            ->once()
            ->andReturn(true)
            ->getMock();

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->with(null)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id'])
            ->once()
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('whereIn')
            ->with('id', $ids)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('id', 'desc')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('appends')
            ->with([])
            ->once()
            ->andReturnSelf()
            ->shouldReceive('links')
            ->withNoArgs()
            ->once()
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->once()
            ->andReturn($return)
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('newCacheRepository')
            ->withNoArgs()
            ->once()
            ->andReturn($repositoryMock);

        $getBulk = $baseRepositoryCache->getBulk(
            $ids,
            [
                'id'
            ],
            'id',
            'desc',
            []
        );

        $this->assertEquals($return, $getBulk);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::getBulk
     */
    public function testGetBulkAndReturnCache()
    {
        $return = [
            'data' => [
                'id' => 'id',
                'name' => 'teste',
            ],
        ];

        $ids = [
            1,
            2,
            '*' => [
                1,
                2,
            ],
        ];

        $repositoryMock = Mockery::mock(Repository::class)
            ->shouldReceive('generateIdentifierByArray')
            ->with($ids['*'])
            ->once()
            ->andReturn(':1:2:')
            ->shouldReceive('getQuery')
            ->with(':bulk:1:2:')
            ->once()
            ->andReturn(json_encode($return))
            ->shouldReceive('setQuery')
            ->never()
            ->andReturn(true)
            ->getMock();

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('select')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('whereIn')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('appends')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('links')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->never()
            ->andReturn($return)
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('newCacheRepository')
            ->withNoArgs()
            ->once()
            ->andReturn($repositoryMock);

        $getBulk = $baseRepositoryCache->getBulk(
            $ids,
            [
                'id'
            ],
            'id',
            'desc',
            []
        );

        $this->assertEquals($return, $getBulk);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::getBulk
     */
    public function testGetBulkWithoutDataAndNotCache()
    {
        $return = [
            'data' => [],
        ];

        $ids = [
            1,
            2,
            '*' => [
                1,
                2,
            ],
        ];

        $repositoryMock = Mockery::mock(Repository::class)
            ->shouldReceive('generateIdentifierByArray')
            ->with($ids['*'])
            ->once()
            ->andReturn(':1:2:')
            ->shouldReceive('getQuery')
            ->with(':bulk:1:2:')
            ->once()
            ->andReturn(null)
            ->shouldReceive('setQuery')
            ->never()
            ->andReturn(true)
            ->getMock();

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->with(null)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id'])
            ->once()
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('whereIn')
            ->with('id', $ids)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('id', 'desc')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('appends')
            ->with([])
            ->once()
            ->andReturnSelf()
            ->shouldReceive('links')
            ->withNoArgs()
            ->once()
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->once()
            ->andReturn($return)
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('newCacheRepository')
            ->withNoArgs()
            ->once()
            ->andReturn($repositoryMock);

        $getBulk = $baseRepositoryCache->getBulk(
            $ids,
            [
                'id'
            ],
            'id',
            'desc',
            []
        );

        $this->assertEquals($return, $getBulk);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::insert
     */
    public function testInsert()
    {
        $id = '123456';

        $now = date('Y-m-d H:i:s');

        $data = [
            'id' => $id,
            'name' => 'teste',
            'array' => [
                'key' => 'value',
            ],
        ];

        $arrayToJson = [
            'id' => $id,
            'name' => 'teste',
            'array' => json_encode([
                'key' => 'value',
            ]),
        ];

        $result = [
            'id' => $id,
            'name' => 'teste',
            'array' => json_encode([
                'key' => 'value',
            ]),
            'created' => $now,
            'modified' => $now,
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->with(null)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('insert')
            ->with($result)
            ->once()
            ->andReturnSelf()
            ->getMock();

        $ulidMock = Mockery::mock(Ulid::class)
            ->shouldReceive('generate')
            ->withNoArgs()
            ->once()
            ->andReturn($id)
            ->getMock();

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidMock,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('arrayToJson')
            ->with($data)
            ->once()
            ->andReturn($arrayToJson)
            ->shouldReceive('returnNow')
            ->withNoArgs()
            ->twice()
            ->andReturn($now);

        $insert = $baseRepositoryCache->insert($data);

        $this->assertEquals($insert, $id);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::insert
     */
    public function testInsertWithCreated()
    {
        $id = '123456';

        $now = date('Y-m-d H:i:s');

        $data = [
            'id' => $id,
            'name' => 'teste',
            'array' => [
                'key' => 'value',
            ],
            'created' => $now,
        ];

        $arrayToJson = [
            'id' => $id,
            'name' => 'teste',
            'array' => json_encode([
                'key' => 'value',
            ]),
            'created' => $now,
        ];

        $result = [
            'id' => $id,
            'name' => 'teste',
            'array' => json_encode([
                'key' => 'value',
            ]),
            'created' => $now,
            'modified' => $now,
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->with(null)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('insert')
            ->with($result)
            ->once()
            ->andReturnSelf()
            ->getMock();

        $ulidMock = Mockery::mock(Ulid::class)
            ->shouldReceive('generate')
            ->withNoArgs()
            ->andReturn($id)
            ->getMock();

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidMock,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('arrayToJson')
            ->with($data)
            ->once()
            ->andReturn($arrayToJson)
            ->shouldReceive('returnNow')
            ->withNoArgs()
            ->once()
            ->andReturn($now);

        $insert = $baseRepositoryCache->insert($data);

        $this->assertEquals($insert, $id);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::update
     */
    public function testUpdate()
    {
        $id = '123456';

        $now = date('Y-m-d H:i:s');

        $data = [
            'name' => 'teste',
            'modified' => $now,
        ];

        $arrayToJson = [
            'name' => 'teste',
            'modified' => $now,
        ];

        $repositoryMock = Mockery::mock(Repository::class)
            ->shouldReceive('delQuery')
            ->with(':'.$id)
            ->once()
            ->andReturn(true)
            ->getMock();

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->with(null)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('id', $id)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('update')
            ->with($arrayToJson)
            ->once()
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('newCacheRepository')
            ->withNoArgs()
            ->once()
            ->andReturn($repositoryMock)
            ->shouldReceive('arrayToJson')
            ->with($data)
            ->once()
            ->andReturn($arrayToJson)
            ->shouldReceive('returnNow')
            ->withNoArgs()
            ->once()
            ->andReturn($now);

        $update = $baseRepositoryCache->update($data, $id);

        $this->assertEquals($update, true);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::delete
     */
    public function testDelete()
    {
        $id = '123456';

        $now = date('Y-m-d H:i:s');

        $data = [
            'modified' => $now,
            'deleted' => $now,
        ];

        $repositoryMock = Mockery::mock(Repository::class)
            ->shouldReceive('delQuery')
            ->with(':'.$id)
            ->once()
            ->andReturn(true)
            ->getMock();

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->with(null)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('id', $id)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('update')
            ->with($data)
            ->once()
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = Mockery::mock(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy,
            ]
        )->makePartial();

        $baseRepositoryCache->shouldReceive('newCacheRepository')
            ->withNoArgs()
            ->once()
            ->andReturn($repositoryMock)
            ->shouldReceive('returnNow')
            ->withNoArgs()
            ->twice()
            ->andReturn($now);

        $delete = $baseRepositoryCache->delete($id);

        $this->assertEquals($delete, true);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::setFilters
     */
    public function testSetFiltersWhere()
    {
        $filters = [
            'active' => [
                'type' => 'eql',
                'data' => 1,
            ],
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('where')
            ->with('active', '=', 1)
            ->once()
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = $this->getMockForAbstractClass(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $setFilter = $baseRepositoryCache->setFilters($dbMock, $filters);

        $this->assertInstanceOf(DatabaseManager::class, $setFilter);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::setFilters
     */
    public function testSetFiltersLike()
    {
        $filters = [
            'active' => [
                'type' => 'lik',
                'data' => 1,
            ],
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('where')
            ->with('active', 'like', '%1%')
            ->once()
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = $this->getMockForAbstractClass(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $setFilter = $baseRepositoryCache->setFilters($dbMock, $filters);

        $this->assertInstanceOf(DatabaseManager::class, $setFilter);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::setFilters
     */
    public function testSetFiltersWhereNull()
    {
        $filters = [
            'active' => [
                'type' => 'nul'
            ],
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('whereNull')
            ->with('active')
            ->once()
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = $this->getMockForAbstractClass(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $setFilter = $baseRepositoryCache->setFilters($dbMock, $filters);

        $this->assertInstanceOf(DatabaseManager::class, $setFilter);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::setFilters
     */
    public function testSetFiltersWhereNotNull()
    {
        $filters = [
            'active' => [
                'type' => 'nnu'
            ],
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('whereNotNull')
            ->with('active')
            ->once()
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = $this->getMockForAbstractClass(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $setFilter = $baseRepositoryCache->setFilters($dbMock, $filters);

        $this->assertInstanceOf(DatabaseManager::class, $setFilter);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::setFilters
     */
    public function testSetFiltersWhithoutValue()
    {
        $filters = [];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('where')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->never()
            ->andReturnSelf()
            ->shouldReceive('whereNotNull')
            ->never()
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = $this->getMockForAbstractClass(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $setFilter = $baseRepositoryCache->setFilters($dbMock, $filters);

        $this->assertInstanceOf(DatabaseManager::class, $setFilter);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::arrayToJson
     */
    public function testArrayToJson()
    {
        $data = [
            'test' => true,
        ];

        $dbSpy = Mockery::spy(DatabaseManager::class);
        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = $this->getMockForAbstractClass(
            BaseRepositoryCache::class,
            [
                $dbSpy,
                $ulidSpy
            ]
        );

        $arrayToJson = $baseRepositoryCache->arrayToJson($data);

        $this->assertEquals($data, $arrayToJson);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::arrayToJson
     */
    public function testArrayToJsonWithArray()
    {
        $data = [
            'test' => true,
            'test2' => [
                'test3' => true,
            ],
        ];

        $result = [
            'test' => true,
            'test2' => json_encode([
                'test3' => true,
            ]),
        ];

        $dbSpy = Mockery::spy(DatabaseManager::class);
        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = $this->getMockForAbstractClass(
            BaseRepositoryCache::class,
            [
                $dbSpy,
                $ulidSpy,
            ]
        );

        $arrayToJson = $baseRepositoryCache->arrayToJson($data);

        $this->assertEquals($result, $arrayToJson);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::beginTrans
     */
    public function testBeginTrans()
    {
        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('beginTransaction')
            ->withNoArgs()
            ->once()
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = $this->getMockForAbstractClass(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $result = $baseRepositoryCache->beginTrans();

        $this->assertTrue($result);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::commit
     */
    public function testCommit()
    {
        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('commit')
            ->withNoArgs()
            ->once()
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = $this->getMockForAbstractClass(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $result = $baseRepositoryCache->commit();

        $this->assertTrue($result);
    }

    /**
     * @covers \App\Repositories\BaseRepositoryCache::rollBack
     */
    public function testRollBack()
    {
        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('rollBack')
            ->withNoArgs()
            ->once()
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepositoryCache = $this->getMockForAbstractClass(
            BaseRepositoryCache::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $result = $baseRepositoryCache->rollBack();

        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
