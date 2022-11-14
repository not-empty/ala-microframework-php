<?php

namespace App\Repositories;

use Illuminate\Database\DatabaseManager;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ulid\Ulid;

class BaseRepositoryTest extends TestCase
{
    /**
     * @covers \App\Repositories\BaseRepository::__construct
     */
    public function testCreateBaseRepository()
    {
        $dbSpy = Mockery::spy(DatabaseManager::class);
        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbSpy,
                $ulidSpy
            ]
        );
        $this->assertInstanceOf(
            BaseRepository::class,
            $baseRepository
        );
    }

    /**
     * @covers \App\Repositories\BaseRepository::getById
     */
    public function testGetById()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('find')
            ->with(1)
            ->andReturn($return)
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $getById = $baseRepository->getById(1);

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepository::getDeadById
     */
    public function testGetDeadById()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('whereNotNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('find')
            ->with(1)
            ->andReturn($return)
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $getById = $baseRepository->getDeadById(1);

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepository::getList
     * @covers \App\Repositories\BaseRepository::setFilters
     * @covers \App\Repositories\BaseRepository::setWheres
     */
    public function testGetListPaginated()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->withArgs(['id', 'desc'])
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25, ['*'], 'page', 2)
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->andReturnSelf()
            ->shouldReceive('links')
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $getById = $baseRepository->getList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            null,
            [
                'page' => 2,
            ]
        );

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepository::getList
     * @covers \App\Repositories\BaseRepository::setFilters
     */
    public function testGetList()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->withArgs(['id', 'desc'])
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25, ['*'], 'page', 1)
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->andReturnSelf()
            ->shouldReceive('links')
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $getById = $baseRepository->getList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            null,
            []
        );

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepository::getList
     * @covers \App\Repositories\BaseRepository::setFilters
     */
    public function testGetListLimit()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
            'per_page' => 10,
        ];

        $limit = 10;

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->withArgs(['id', 'desc'])
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with($limit, ['*'], 'page', 1)
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->andReturnSelf()
            ->shouldReceive('links')
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $getList = $baseRepository->getList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            null,
            [
                'limit' => $limit,
            ]
        );

        $this->assertEquals($return, $getList);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['per_page'], $limit);
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepository::getDeadList
     */
    public function testGetDeadList()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->andReturnSelf()
            ->shouldReceive('whereNotNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->withArgs(['id', 'desc'])
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25, ['*'], 'page', 1)
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->andReturnSelf()
            ->shouldReceive('links')
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $getById = $baseRepository->getDeadList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            null,
            []
        );

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepository::getDeadList
     * @covers \App\Repositories\BaseRepository::setWheres
     */
    public function testGetDeadListPaginated()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->andReturnSelf()
            ->shouldReceive('whereNotNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->withArgs(['id', 'desc'])
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25, ['*'], 'page', 4)
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->andReturnSelf()
            ->shouldReceive('links')
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $getById = $baseRepository->getDeadList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            null,
            [
                'page' => 4,
            ]
        );

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

        /**
     * @covers \App\Repositories\BaseRepository::getDeadList
     * @covers \App\Repositories\BaseRepository::setWheres
     */
    public function testGetDeadListLimit()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
            'per_page' => 10,
        ];

        $limit = 10;

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->andReturnSelf()
            ->shouldReceive('whereNotNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->withArgs(['id', 'desc'])
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with($limit, ['*'], 'page', 1)
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->andReturnSelf()
            ->shouldReceive('links')
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $getDeadList = $baseRepository->getDeadList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            null,
            [
                'limit' => $limit,
            ]
        );

        $this->assertEquals($return, $getDeadList);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['per_page'], $limit);
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepository::insert
     * @covers \App\Repositories\BaseRepository::arrayToJson
     */
    public function testInsert()
    {
        $id = '123456';

        $data = [
            'id' => $id,
            'name' => 'teste',
            'array' => [
                'key' => 'value',
            ],
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('insert')
            ->andReturnSelf()
            ->getMock();

        $ulidMock = Mockery::mock(Ulid::class)
            ->shouldReceive('generate')
            ->withNoArgs()
            ->andReturn($id)
            ->getMock();

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidMock
            ]
        );

        $insert = $baseRepository->insert($data);

        $this->assertEquals($insert, $id);
    }

    /**
     * @covers \App\Repositories\BaseRepository::insert
     */
    public function testInsertWithCreated()
    {
        $id = '123456';

        $data = [
            'id' => $id,
            'name' => 'teste',
            'created' => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('insert')
            ->with($data)
            ->andReturnSelf()
            ->getMock();

        $ulidMock = Mockery::mock(Ulid::class)
            ->shouldReceive('generate')
            ->withNoArgs()
            ->andReturn($id)
            ->getMock();

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidMock
            ]
        );

        $insert = $baseRepository->insert($data);

        $this->assertEquals($insert, $id);
    }

    /**
     * @covers \App\Repositories\BaseRepository::update
     */
    public function testUpdate()
    {
        $id = '123456';

        $data = [
            'name' => 'teste',
            'modified' => date('Y-m-d H:i:s'),
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('id', $id)
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('update')
            ->with($data)
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $update = $baseRepository->update($data, $id);

        $this->assertEquals($update, true);
    }

    /**
     * @covers \App\Repositories\BaseRepository::delete
     */
    public function testDelete()
    {
        $id = '123456';

        $data = [
            'modified' => date('Y-m-d H:i:s'),
            'deleted' => date('Y-m-d H:i:s'),
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('id', $id)
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('update')
            ->with($data)
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $delete = $baseRepository->delete($id);

        $this->assertEquals($delete, true);
    }

    /**
     * @covers \App\Repositories\BaseRepository::getList
     * @covers \App\Repositories\BaseRepository::setFilters
     */
    public function testGetListWithLike()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->withArgs(['id', 'desc'])
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25, ['*'], 'page', 1)
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->andReturnSelf()
            ->shouldReceive('links')
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('user_name', 'like', '%dim%')
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $getById = $baseRepository->getList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            [
                'user_name' => [
                    'type' => 'lik',
                    'data' => 'dim'
                ]
            ],
            []
        );

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepository::getList
     * @covers \App\Repositories\BaseRepository::setFilters
     */
    public function testGetListWithEqual()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->withArgs(['id', 'desc'])
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25, ['*'], 'page', 1)
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->andReturnSelf()
            ->shouldReceive('links')
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('user_name', '=', 'dim')
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $getById = $baseRepository->getList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            [
                'user_name' => [
                    'type' => 'eql',
                    'data' => 'dim'
                ]
            ],
            []
        );

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepository::getList
     * @covers \App\Repositories\BaseRepository::setFilters
     */
    public function testGetListWithNull()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->withArgs(['id', 'desc'])
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25, ['*'], 'page', 1)
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->andReturnSelf()
            ->shouldReceive('links')
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('user_name')
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $getById = $baseRepository->getList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            [
                'user_name' => [
                    'type' => 'nul'
                ]
            ],
            []
        );

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepository::getList
     * @covers \App\Repositories\BaseRepository::setFilters
     */
    public function testGetListWithNotNull()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id', 'user_name'])
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->withArgs(['id', 'desc'])
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25, ['*'], 'page', 1)
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->andReturn($return)
            ->shouldReceive('appends')
            ->andReturnSelf()
            ->shouldReceive('links')
            ->andReturnSelf()
            ->shouldReceive('whereNotNull')
            ->with('user_name')
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $getById = $baseRepository->getList(
            [
                'id',
                'user_name'
            ],
            'id',
            'desc',
            [
                'user_name' => [
                    'type' => 'nnu'
                ]
            ],
            []
        );

        $this->assertEquals($return, $getById);
        $this->assertEquals($return['id'], 'id');
        $this->assertEquals($return['name'], 'teste');
    }

    /**
     * @covers \App\Repositories\BaseRepository::getBulk
     */
    public function testGetBulk()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id'])
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('whereIn')
            ->with('id', [1, 2])
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('id', 'desc')
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with(25)
            ->andReturnSelf()
            ->shouldReceive('appends')
            ->with([])
            ->andReturnSelf()
            ->shouldReceive('links')
            ->withNoArgs()
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->andReturn($return)
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $getBulk = $baseRepository->getBulk(
            [
                1,
                2
            ],
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
     * @covers \App\Repositories\BaseRepository::getBulk
     */
    public function testGetBulkLimit()
    {
        $return = [
            'id' => 'id',
            'name' => 'teste',
        ];

        $limit = 1;

        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('table')
            ->andReturnSelf()
            ->shouldReceive('select')
            ->with(['id'])
            ->andReturnSelf()
            ->shouldReceive('whereNull')
            ->with('deleted')
            ->andReturnSelf()
            ->shouldReceive('whereIn')
            ->with('id', [1, 2])
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('id', 'desc')
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->with($limit)
            ->andReturnSelf()
            ->shouldReceive('appends')
            ->with(['limit' => $limit])
            ->andReturnSelf()
            ->shouldReceive('links')
            ->withNoArgs()
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->andReturn($return)
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $getBulk = $baseRepository->getBulk(
            [
                1,
                2
            ],
            [
                'id'
            ],
            'id',
            'desc',
            [
                'limit' => 1,
            ]
        );

        $this->assertEquals($return, $getBulk);
    }

    /**
     * @covers \App\Repositories\BaseRepository::beginTrans
     */
    public function testBeginTrans()
    {
        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('beginTransaction')
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $result = $baseRepository->beginTrans();

        $this->assertTrue($result);
    }

    /**
     * @covers \App\Repositories\BaseRepository::commit
     */
    public function testCommit()
    {
        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('commit')
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $result = $baseRepository->commit();

        $this->assertTrue($result);
    }

    /**
     * @covers \App\Repositories\BaseRepository::rollBack
     */
    public function testRollBack()
    {
        $dbMock = Mockery::mock(DatabaseManager::class)
            ->shouldReceive('rollBack')
            ->andReturnSelf()
            ->getMock();

        $ulidSpy = Mockery::spy(Ulid::class);

        $baseRepository = $this->getMockForAbstractClass(
            BaseRepository::class,
            [
                $dbMock,
                $ulidSpy
            ]
        );

        $result = $baseRepository->rollBack();

        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
