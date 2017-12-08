<?php
use PHPUnit\Framework\TestCase;

class PDODQLTest extends TestCase
{
    protected static $db;

    protected static $pdo;

    public static function setUpBeforeClass()
    {

    }

    public static function tearDownAfterClass()
    {
        self::$pdo = NULL;
        self::$db  = NULL;
    }

    public function testSetGetTable()
    {
        $this->assertEquals('t_user', self::$db->table('user')->getTable());
    }

    public function testGet()
    {
        $expect = self::$pdo->query('SELECT * FROM t_user ')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->get();

        $this->assertEquals($expect, $testResult);
    }

    public function testRow()
    {
        $expect = self::$pdo->query('SELECT * FROM t_user ')
                ->fetch(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->row();

        $this->assertEquals($expect, $testResult);
    }

    public function testList()
    {
        $expect = self::$pdo->query('SELECT username FROM t_user ')
                ->fetchAll(PDO::FETCH_COLUMN, 0);
        $testResult = self::$db->table('user')->list('username');

        $this->assertEquals($expect, $testResult);
    }

    public function testCount()
    {
        $expect = self::$pdo->query('SELECT COUNT(*) AS count_num FROM t_user ')
                ->fetch(PDO::FETCH_ASSOC)['count_num'];
        $testResult = self::$db->table('user')->count('*');

        $this->assertEquals($expect, $testResult);
    }

    public function testSum()
    {
        $expect = self::$pdo->query('SELECT SUM(id) AS sum_num FROM t_user ')
                ->fetch(PDO::FETCH_ASSOC)['sum_num'];
        $testResult = self::$db->table('user')->sum('id');

        $this->assertEquals($expect, $testResult);
    }

    public function testMax()
    {
        $expect = self::$pdo->query('SELECT MAX(id) AS max_num FROM t_user ')
                ->fetch(PDO::FETCH_ASSOC)['max_num'];
        $testResult = self::$db->table('user')->max('id');

        $this->assertEquals($expect, $testResult);
    }

    public function testMin()
    {
        $expect = self::$pdo->query('SELECT MIN(id) AS min_num FROM t_user ')
                ->fetch(PDO::FETCH_ASSOC)['min_num'];
        $testResult = self::$db->table('user')->min('id');

        $this->assertEquals($expect, $testResult);
    }

    public function testAvg()
    {
        $expect = self::$pdo->query('SELECT AVG(id) AS avg_num FROM t_user ')
                ->fetch(PDO::FETCH_ASSOC)['avg_num'];
        $testResult = self::$db->table('user')->avg('id');

        $this->assertEquals($expect, $testResult);
    }

    public function testSelect()
    {
        // field
        $expect = self::$pdo->query('SELECT username, email FROM t_user ')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->select('username', 'email')->get();

        $this->assertEquals($expect, $testResult);
        // *
        $expect = self::$pdo->query('SELECT * FROM t_user ')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->select('*')->get();

        $this->assertEquals($expect, $testResult);

    }

    public function testWhere()
    {
        // where
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE g_id = 3')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where('g_id', 3)->get();

        $this->assertEquals($expect, $testResult);

        // where = param
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE id = 20')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where('id', '=', 20)->get();

        $this->assertEquals($expect, $testResult);

        // where > param
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE id > 20')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where('id', '>', 20)->get();

        $this->assertEquals($expect, $testResult);

        // where < param
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE id < 20')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where('id', '<', 20)->get();

        $this->assertEquals($expect, $testResult);

        // where <= param
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE id <= 20')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where('id', '<=', 20)->get();

        $this->assertEquals($expect, $testResult);

        // where >= param
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE id >= 20')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where('id', '>=', 20)->get();

        $this->assertEquals($expect, $testResult);

        // where !=
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE id != 20')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where('id', '!=', 20)->get();

        $this->assertEquals($expect, $testResult);

        // where <>
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE id <> 20')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where('id', '<>', 20)->get();

        $this->assertEquals($expect, $testResult);

        // where like
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE username LIKE \'%a%\' ')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where('username', 'like', '%a%')->get();

        $this->assertEquals($expect, $testResult);
        // where not like
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE username NOT LIKE \'%a%\' ')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where('username', 'NOT LIKE', '%a%')->get();

        $this->assertEquals($expect, $testResult);
        // where is null
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE id IS NULL ')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where('id', NULL)->get();

        $this->assertEquals($expect, $testResult);

        // where array param
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE sort_num = 20 AND activated = 0')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')->where(['sort_num' => 20, 'activated' => 0])->get();

        $this->assertEquals($expect, $testResult);

        // and where
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE (sort_num = 20 AND activated = 0 AND id = 24)')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->where(['sort_num' => 20, 'activated' => 0])
            ->where('id', 24)
            ->get();

        $this->assertEquals($expect, $testResult);

        // or where
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE id < 20 OR id >= 100')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->where('id', '<', 20)
            ->orWhere('id', '>=', 100)
            ->get();

        $this->assertEquals($expect, $testResult);

        // or where array param
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE id >= 100 OR (sort_num = 50 AND activated = 1)')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->where('id', '>=', 100)
            ->orWhere(['sort_num' => 50, 'activated' => 1])
            ->get();

        $this->assertEquals($expect, $testResult);

        // where like

    }

    public function testWhereIn()
    {
        // where in
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE id IN (1, 2, 20, 30, 21)')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->whereIn('id', [1, 2, 20, 30, 21])
            ->get();

        $this->assertEquals($expect, $testResult);

        // where not in
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE id NOT IN (1, 2, 20, 30, 21)')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->whereNotIn('id', [1, 2, 20, 30, 21])
            ->get();

        $this->assertEquals($expect, $testResult);

        // or where in
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE id = 3 OR id IN (1, 2, 20, 30, 21)')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->Where('id', '=', 3)
            ->orWhereIn('id', [1, 2, 20, 30, 21])
            ->get();

        $this->assertEquals($expect, $testResult);
        // or where not in
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE id != 3 OR id NOT IN (1, 2, 20, 30, 21)')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->Where('id', '!=', 3)
            ->orWhereNotIn('id', [1, 2, 20, 30, 21])
            ->get();

        $this->assertEquals($expect, $testResult);
    }

    public function testWhereBetween()
    {
        // where between
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE id BETWEEN 20 and 30')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->whereBetween('id', 20, 30)
            ->get();

        $this->assertEquals($expect, $testResult);

        // or where between
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE id = 1 OR id BETWEEN 20 and 30')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->where('id', 1)
            ->orWhereBetween('id', 20, 30)
            ->get();

        $this->assertEquals($expect, $testResult);
    }

    public function testWhereNull()
    {
        // where null
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE username IS NULL')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->whereNull('username')
            ->get();

        $this->assertEquals($expect, $testResult);

        // where not null
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE username IS NOT NULL')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->whereNotNull('username')
            ->get();

        $this->assertEquals($expect, $testResult);

        // or where NULL
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE id = 5 OR username IS NULL')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->where('id', 5)
            ->orWhereNull('username')
            ->get();

        $this->assertEquals($expect, $testResult);

        // or where not NULL
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE id = 5 OR username IS NOT NULL')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->where('id', 5)
            ->orWhereNotNull('username')
            ->get();

        $this->assertEquals($expect, $testResult);
    }

    public function testWhereBrackets()
    {
        // Where Brackets
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE (id < 50 OR username IS NOT NULL) AND sort_num = 20')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->whereBrackets(function($query) {
                $query->where('id', '<', 50)
                      ->orWhereNotNull('username');
            })
            ->where('sort_num', 20)
            ->get();

        $this->assertEquals($expect, $testResult);

        // or Where Brackets
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE sort_num = 20 OR (id < 10 AND id > 5)')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->where('sort_num', 20)
            ->orWhereBrackets(function($query) {
                $query->where('id', '<', 10)
                      ->where('id', '>', 5);
            })
            ->get();

        $this->assertEquals($expect, $testResult);

        // Where Brackets sub
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE (id < 50 AND (sort_num = 20 OR activated != 1)) AND id != 20')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->whereBrackets(function($query) {
                $query->where('id', '<', 50)
                      ->whereBrackets(function($query) {
                          $query->where('sort_num', 20)
                                ->orWhere('activated', '!=', 1);
                      });
            })
            ->where('id', '!=', 20)
            ->get();

        $this->assertEquals($expect, $testResult);
    }

    public function testWhereExists()
    {
        // where Exist
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE EXISTS ( SELECT * FROM t_user_group WHERE ID = 3 ) AND g_id = 3')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->whereExists(function($query) {
                $query->table('user_group')->where('id', 3);
            })
            ->where('g_id', 3)
            ->get();

        $this->assertEquals($expect, $testResult);

        // where Not Exist
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE NOT EXISTS ( SELECT * FROM t_user_group WHERE ID = 3 ) AND g_id = 3')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->whereNotExists(function($query) {
                $query->table('user_group')->where('id', 3);
            })
            ->where('g_id', 3)
            ->get();

        $this->assertEquals($expect, $testResult);

        // or where Exist
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE g_id = 3 OR EXISTS ( SELECT * FROM t_user_group WHERE ID = 3 )')
                ->fetchAll(PDO::FETCH_ASSOC);

        $testResult = self::$db->table('user')
            ->where('g_id', 3)
            ->orWhereExists(function($query) {
                $query->table('user_group')->where('id', 3);
            })
            ->get();

        $this->assertEquals($expect, $testResult);

        // or where Not Exist
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE g_id = 3 OR NOT EXISTS ( SELECT * FROM t_user_group WHERE ID = 3 )')
                ->fetchAll(PDO::FETCH_ASSOC);

        $testResult = self::$db->table('user')
            ->where('g_id', 3)
            ->orWhereNotExists(function($query) {
                $query->table('user_group')->where('id', 3);
            })
            ->get();

        $this->assertEquals($expect, $testResult);

        // more complex
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE username = \'Jackie aa\' OR ( NOT EXISTS ( SELECT * FROM t_user WHERE username = \'Jackie aa\' ) AND username = \'Jackie Conroy\' )')
                ->fetchAll(PDO::FETCH_ASSOC);

        $testResult = self::$db->table('user')
            ->where('username', 'Jackie aa')
            ->orWhereBrackets(function($query) {
                $query->whereNotExists(function($query) {
                    $query->table('user')->where('username', 'Jackie aa');
                })->where('username', 'Jackie Conroy');
            })
            ->get();

        $this->assertEquals($expect, $testResult);

    }

    public function testWhereInSub()
    {
        // where in sub
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE g_id IN ( SELECT id FROM t_user_group) ')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->whereInSub('g_id', function($query) {
                $query->table('user_group')->select('id');
            })
            ->get();

        $this->assertEquals($expect, $testResult);

        // where not in sub
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE g_id NOT IN ( SELECT id FROM t_user_group) ')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->whereNotInSub('g_id', function($query) {
                $query->table('user_group')->select('id');
            })
            ->get();

        $this->assertEquals($expect, $testResult);

        // or where in sub
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE g_id != 1 OR g_id IN ( SELECT id FROM t_user_group) ')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->where('g_id', '!=', 1)
            ->orWhereInSub('g_id', function($query) {
                $query->table('user_group')->select('id');
            })
            ->get();

        $this->assertEquals($expect, $testResult);

        // or where not in sub
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE g_id != 1 OR g_id NOT IN ( SELECT id FROM t_user_group) ')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->where('g_id', '!=', 1)
            ->orWhereNotInSub('g_id', function($query) {
                $query->table('user_group')->select('id');
            })
            ->get();

        $this->assertEquals($expect, $testResult);
    }


    public function testFromSub()
    {
        // fromSub
        $expect = self::$pdo->query('SELECT id, username, email FROM ( SELECT * FROM t_user WHERE id < 20 ) AS tb_1 ')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->select('id', 'username', 'email')
            ->fromSub(function($query) {
                $query->table('user')->where('id', '<', '20');
            })
            ->get();

        $this->assertEquals($expect, $testResult);
    }


    public function testGroupBy()
    {
        $expect = self::$pdo->query('SELECT sort_num, COUNT(sort_num) FROM t_user GROUP BY sort_num')
                ->fetchAll(PDO::FETCH_ASSOC);

        $testResult = self::$db->table('user')
            ->select('sort_num', 'COUNT(sort_num)')
            ->groupBy('sort_num')
            ->get();

        $this->assertEquals($expect, $testResult);

    }

    public function testHaving()
    {
        // having 3 param
        $expect = self::$pdo->query('SELECT sort_num, COUNT(sort_num) FROM t_user GROUP BY sort_num HAVING COUNT(sort_num) < 20')
                ->fetchAll(PDO::FETCH_ASSOC);

        $testResult = self::$db->table('user')
            ->select('sort_num', 'COUNT(sort_num)')
            ->groupBy('sort_num')
            ->having('COUNT(sort_num)', '<', 20)
            ->get();

        $this->assertEquals($expect, $testResult);

        // having 2 param
        $expect = self::$pdo->query('SELECT sort_num, COUNT(sort_num) FROM t_user GROUP BY sort_num HAVING COUNT(sort_num) = 3')
                ->fetchAll(PDO::FETCH_ASSOC);

        $testResult = self::$db->table('user')
            ->select('sort_num', 'COUNT(sort_num)')
            ->groupBy('sort_num')
            ->having('count(sort_num)', 3)
            ->get();

        $this->assertEquals($expect, $testResult);

        // having array param
        $expect = self::$pdo->query('SELECT sort_num, activated FROM t_user GROUP BY sort_num, activated HAVING sort_num = 20 AND activated = 0')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->select('sort_num', 'activated')
            ->groupBy('sort_num')
            ->groupBy('activated')
            ->having(['sort_num' => 20, 'activated' => 0])
            ->get();

        $this->assertEquals($expect, $testResult);

        // or having
        $expect = self::$pdo->query('SELECT sort_num, activated FROM t_user GROUP BY sort_num, activated HAVING sort_num = 20 OR sort_num = 50')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->select('sort_num', 'activated')
            ->groupBy('sort_num')
            ->groupBy('activated')
            ->having('sort_num', 20)
            ->orHaving('sort_num', 50)
            ->get();

        $this->assertEquals($expect, $testResult);
    }

    public function testOrderBy()
    {
        // order by
        $expect = self::$pdo->query('SELECT * FROM t_user ORDER BY id')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->orderBy('id')
            ->get();

        $this->assertEquals($expect, $testResult);

        // desc
        $expect = self::$pdo->query('SELECT * FROM t_user ORDER BY id DESC')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->orderBy('id', 'DESC')
            ->get();

        $this->assertEquals($expect, $testResult);

        // asc
        $expect = self::$pdo->query('SELECT * FROM t_user ORDER BY id ASC')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->orderBy('id', 'ASC')
            ->get();

        $this->assertEquals($expect, $testResult);

        // order by more field
        $expect = self::$pdo->query('SELECT * FROM t_user ORDER BY sort_num DESC, id ASC')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->orderBy('sort_num', 'DESC')
            ->orderBy('id', 'ASC')
            ->get();

        $this->assertEquals($expect, $testResult);

    }

    public function testLimit()
    {
        $expect = self::$pdo->query('SELECT * FROM t_user LIMIT 10 OFFSET 3')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->limit(3, 10)
            ->get();

        $this->assertEquals($expect, $testResult);

    }

    public function testPaginate()
    {
        $expect = [];
        $expect['total']        = self::$pdo->query('SELECT COUNT(*) as count_num FROM t_user')
                ->fetch(PDO::FETCH_ASSOC)['count_num'];
        $expect['per_page']     = 10;
        $expect['current_page'] = 2;
        $expect['next_page']    = 3;
        $expect['prev_page']    = 1;
        $expect['first_page']   = 1;
        $expect['last_page']    = $expect['total'] / 10;
        $expect['data']         = self::$pdo->query('SELECT * FROM t_user LIMIT 10 OFFSET 10')
                ->fetchAll(PDO::FETCH_ASSOC);

        $testResult = self::$db->table('user')
            ->paginate(10, 2);

        $this->assertEquals($expect, $testResult);
    }

    public function testJoin()
    {
        // inner join
        $expect = self::$pdo->query('SELECT * FROM t_user INNER JOIN t_user_group ON t_user.g_id = t_user_group.id')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->join('user_group', 'user.g_id', 'user_group.id')
            ->get();

        $this->assertEquals($expect, $testResult);

        // left join
        $expect = self::$pdo->query('SELECT t_user.username, t_user_group.groupname FROM t_user LEFT JOIN t_user_group ON t_user.g_id = t_user_group.id')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->select('user.username', 'user_group.groupname')
            ->leftJoin('user_group', 'user.g_id', 'user_group.id')
            ->get();

        $this->assertEquals($expect, $testResult);
    }

    public function testComplex()
    {
        // sub query with join
        $expect = self::$pdo->query('SELECT t_user.username, t_user_group.groupname FROM t_user LEFT JOIN t_user_group ON t_user.g_id = t_user_group.id WHERE username = \'Jackie aa\' OR ( NOT EXISTS ( SELECT * FROM t_user WHERE username = \'Jackie aa\' ) AND username = \'Jackie Conroy\' )')
                ->fetchAll(PDO::FETCH_ASSOC);

        $testResult = self::$db->table('user')
            ->select('user.username', 'user_group.groupname')
            ->leftJoin('user_group', 'user.g_id', 'user_group.id')
            ->where('user.username', 'Jackie aa')
            ->orWhereBrackets(function($query) {
                $query->whereNotExists(function($query) {
                    $query->table('user')->where('username', 'Jackie aa');
                })->where('user.username', 'Jackie Conroy');
            })
            ->get();

        $this->assertEquals($expect, $testResult);

        // group by with join
        $expect = self::$pdo->query('SELECT t_user.sort_num, COUNT(*) FROM t_user INNER JOIN t_user_group ON t_user.g_id = t_user_group.id WHERE t_user.activated <> 0 GROUP BY t_user.sort_num HAVING t_user.sort_num = 20 OR t_user.sort_num = 50 ORDER BY t_user.sort_num DESC')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->select('user.sort_num', 'COUNT(*)')
            ->join('user_group', 'user.g_id', 'user_group.id')
            ->where('user.activated', '<>', 0)
            ->groupBy('user.sort_num')
            ->having('user.sort_num', '50')
            ->orHaving('user.sort_num', '20')
            ->orderBy('user.sort_num', 'DESC')
            ->get();

        $this->assertEquals($expect, $testResult);

        // multi join
        $expect = self::$pdo->query('SELECT t_user.username, t_user_group.groupname, t_company.companyname FROM t_company LEFT JOIN t_user_group ON t_user_group.c_id = t_company.id LEFT JOIN t_user ON t_user.g_id = t_user_group.id ORDER BY t_user.sort_num ASC, t_user.id DESC LIMIT 25 offset 10')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('company')
            ->select('user.username', 'user_group.groupname', 'company.companyname')
            ->leftJoin('user_group', 'user_group.c_id', 'company.id')
            ->leftJoin('user', 'user.g_id', 'user_group.id')
            ->orderBy('user.sort_num', 'ASC')
            ->orderBy('user.id', 'DESC')
            ->limit(10, 25)
            ->get();

        $this->assertEquals($expect, $testResult);

        // more complex
        $expect = self::$pdo->query('SELECT * FROM t_user WHERE username = \'Jackie aa\' OR ( NOT EXISTS ( SELECT * FROM t_user WHERE username = \'Jackie aa\' ) AND (username = \'Jackie Conroy\' OR username = \'Jammie Haag\')) AND g_id IN ( SELECT id FROM t_user_group) ORDER BY id DESC LIMIT 1 OFFSET 0 ')
                ->fetchAll(PDO::FETCH_ASSOC);
        $testResult = self::$db->table('user')
            ->where('username', 'Jackie aa')
            ->orWhereBrackets(function($query) {
                $query->whereNotExists(function($query) {
                    $query->table('user')->where('username', 'Jackie aa');
                })->WhereBrackets(function($query) {
                    $query->where('username', 'Jackie Conroy')
                          ->orWhere('username', 'Jammie Haag');
                });
            })
            ->whereInSub('g_id', function($query) {
                $query->table('user_group')->select('id');
            })
            ->orderBy('id', 'DESC')
            ->limit(0, 1)
            ->get();

        $this->assertEquals($expect, $testResult);
    }

}
