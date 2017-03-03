<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * Default Controller of webapp.
 */
class IndexController extends ArController
{
    // 初始化方法
    public function init()
    {

    }

    // 生成验证码
    public function confirmAction()
    {
        session_start();
        $_vc = new ValidateCode(); // 实例化一个对象
        $_vc->doimg();
        $_SESSION['code'] = $_vc->getCode();// 验证码保存到SESSION中

    }

    // 加密
    public function pwd($str)
    {
        return md5(substr(md5(md5($str).'listen'), 6, 6));

    }

    // 生成 key
    public function keytoken()
    {
        $utils = new Utils();
        $str = $utils::random(6, $type = 0, $hash = '');
        return md5(substr(md5(md5($str).time()), 6, 6));

    }

    // 判断用户是否登陆的跳转页面
    public function isLogin()
    {
        if (!arModule('Lib.User')->isLogin()) {
            $this -> redirect('Index/index');
        }

    }
    // 用户修改个人信息
    public function updateInfoAction()
    {
        $this->assign(array('page_title' => '修改个人信息'));
        $this->display(); 
    }

    // 查询项目的状态
    public function itemStatus()
    {
        return U_item_status_typeModel::model() -> getDb() -> queryAll();
    }

    // 查询所有部门
    public function department()
    {
        return U_departmentModel::model() -> getDb() -> select('id, d_name') -> queryAll();
    }

    // 查询谋部门的职位
    public function jobs($str)
    {
        return U_department_jobsModel::model() -> getDb() -> select('id, j_name') -> where('j_did = '.$str) -> queryAll();
    }

    // 查询谋职位的成员
    public function users($str)
    {
        return U_usersModel::model() -> getDb() -> select('id, nickname, tel, weixin, qq, email') -> where('job like "%'.$str.'%"') -> queryAll();
    }

    // 项目在队的队员合并 $str 是项目id
    public function itemUserAll($str){
        $users = U_item_taskModel::model() -> getDb() -> select('u_id') -> where('i_id='.$str.' and stay=1') -> order('id desc') -> queryAll();
        foreach ($users as $key => $value) {
            $arr[] = $value['u_id'];
        }
        $arr = array_unique($arr);
        //将数组的值用字符串链接
        $users = implode(',', $arr);
        return $users;
    }

    // 删除某个项目

    public function delItem($id){
        $i_id = $id;
        $i_name = U_itemsModel::model() -> getDb() -> select('i_name') -> where('id='.$i_id) -> queryRow();
        $falg = U_itemsModel::model() -> getDb() -> where('id='.$i_id) -> update(array('online' => 0));
        if ($falg) {

            // 记录到进度表
            $content = '删除项目<b>'.$i_name['i_name'].'</b>成功!';
            // '项目概述','项目变更','成员变更', '任务变更', '提成变更'
            $type =  2;
            $loginsert = array('content' => $content, 'type' => $type, 'u_id' => $_SESSION['u_id'], 'i_id' => $i_id, 'Date' => date('Y-m-d H:i:s'));

            $log = U_item_develop_logModel::model() -> getDb() -> insert($loginsert);
            // $this -> redirectSuccess('Index/pm', '删除项目成功！', '3');
            return true;
        }else{

            // $this -> redirectError('Index/pm',  '删除项目失败！', '3');
            return false;
        }

    }

    // 恢复某个项目

    public function recoveryItem($id){
        $i_id = $id;
        $i_name = U_itemsModel::model() -> getDb() -> select('i_name') -> where('id='.$i_id) -> queryRow();
        $falg = U_itemsModel::model() -> getDb() -> where('id='.$i_id) -> update(array('online' => 1));
        if ($falg) {

            // 记录到进度表
            $content = '恢复项目<b>'.$i_name['i_name'].'</b>成功!';
                    // '项目概述','项目变更','成员变更'
            $type =  2;
            $loginsert = array('content' => $content, 'type' => $type, 'u_id' => $_SESSION['u_id'], 'i_id' => $i_id, 'Date' => date('Y-m-d H:i:s'));

            $log = U_item_develop_logModel::model() -> getDb() -> insert($loginsert);
            // $this -> redirectSuccess('Index/pm', '删除项目成功！', '3');
            return true;
        }else{

            // $this -> redirectError('Index/pm',  '删除项目失败！', '3');
            return false;
        }

    }

    // 所有部门的所有职位
    public function allDepJob(){
        // 查询所有部门
        $data = U_departmentModel:: model() -> getDb() -> select('id, d_name') -> queryAll();
        foreach ($data as $key => &$value) {
            // 获取所有的职位
            $jobs = U_department_jobsModel::model() -> getDb() -> select('id, j_name') -> where('j_did='.$value['id']) -> queryAll();
            if (count($jobs)) {
                $value['job'] = $jobs;
            }else{
                $value['job'] = array();
            }
        }
        return $data;
    }

    // login 和 register 页面
    public function indexAction()
    {
        $this->assign(array('page_title' => '登录注册'));
        // exit;
        $from = '';
        if (arGET()) {
            $from = arGET('from'); // 从其他页面跳转过来的标志
        }
        if (arPOST()) {
            // 过滤字符
            $utils = new Utils();
            $_POST = $utils::shtmlspecialchars(arPOST());

            if (isset($_POST['sign']) && $_POST['sign'] == 'login') {
                # 用户登录...
                // 默认值
                // $key = '';

                // $_SESSION['code'] = 1234;// 测试用
                if (strtolower($_POST['verifyCode']) == $_SESSION['code'] ) {
                    $condition = array(
                        'tel' => arPost('tel'),
                    );
                    # 验证码正确...
                    // 验证密码 1. 通过用户名找密码 2. 判断
                    $pwd = U_usersModel::model() -> getDb() -> select('id, nickname, password') -> where($condition) -> queryRow();
                    if ($pwd) {
                        // 找到密码
                        if ($this -> pwd($_POST['pwd']) === $pwd['password']) {
                            // 生成 key 值
                            // $key = $this -> keytoken();

                            // session 保存数据
                            // arComp('list.session') -> set('key', $key);
                            arComp('list.session') -> set('u_id', $pwd['id']);
                            arComp('list.session') -> set('nickname', $pwd['nickname']);

                            // 发送微信登录消息
                            arModule('wechat.Send')->TplUserLogin($pwd['id']);

                            if ($from) {
                                # code...
                                $ret_code = 1003; // 从项目详情页面跳转来的
                            }else{
                                $ret_code = 1000;// 成功
                            }
                        }else{
                            $ret_code = 1002;// 用户名与密码不匹配
                        }

                    }else{
                        //未找到密码
                        $ret_code = 1002;// 用户名与密码不匹配
                    }

                }else{
                    $ret_code = 1001;// 验证码错误
                }

                // $data = array('ret_code'=>$ret_code, 'key'=>$key);
                if ($url = arComp('list.session')->get('ar_back_url')) :
                    arComp('list.session')->set('ar_back_url', null);
                else :
                    $url = '';
                endif;
                $data = array('ret_code' => $ret_code, 'url' => $url);
                echo json_encode($data);
                exit;

            }elseif (isset($_POST['sign']) && $_POST['sign'] === 'register') {
                # 手机号注册
                $ret_code = 1000;// register 成功 默认值
                $retMsg = '';
                // $_SESSION['code'] = 1234;//测试用
                // 1. 验证码的验证 2.手机号的验证（号码正确即手机号是可用的未注册的 3.密码验证）
                if (strtolower($_POST['verifyCode']) == $_SESSION['code']) {
                    # 验证码正确
                    // 暂时不验证手机
                    // if ($utils::IsMobile($_POST['tel'])) {
                    if (true) {
                        $condition = array(
                            'tel' => arPost('tel'),
                        );
                        # 正确的手机格式
                        $tel = U_usersModel::model() -> getDb()
                            ->select('tel')
                            ->where($condition)
                            ->queryColumn();

                        if ($tel) {
                            # 手机号已注册...
                            //$ret_code = 1003;// 手机号已注册
                            $retMsg = '手机号已注册';
                        }else{
                            // 该手机号可以注册
                            // 密码验证
                            // $reg = "/^[a-zA-Z]\w{5,14}$/";# 以字母开头,长度6,只能包含字符、数字和下划线...
                            $reg = "/^\w{6,16}$/";
                            # 长度6-16,只能包含字符、数字和下划线...
                            if (preg_match($reg,$_POST['pwd'])) {

                                if ($_POST['pwd'] === $_POST['pwd2']) {
                                    # 两次密码一致，向数据库存放数据...
                                    $insertdata = array('tel' => $_POST['tel'], 'password' => $this-> pwd($_POST['pwd']), 'registerDate' => date('Y-m-d H:i:s'), 'level' => 3 );
                                    $flage = U_usersModel::model() -> getDb() -> insert($insertdata);
                                    if ($flage) {
                                        // session 保存数据
                                        arComp('list.session') -> set('u_id', $flage);
                                        arComp('list.session') -> set('tel', $_POST['tel']);
                                        // $ret_code = 1000;// 成功
                                    }
                                }else{
                                     // $ret_code = 1005;// 两次密码不一致
                                     $retMsg = '两次密码不一致';
                                }

                            }else{
                                // $ret_code = 1004;// 密码格式有误
                                $retMsg = '密码格式有误';
                            }

                        }

                    }else{
                        //$ret_code = 1002;// 手机号有误
                        $retMsg = '手机号有误';
                    }

                }else{
                    //$ret_code = 1001;// 验证码错误
                    $retMsg = '验证码错误';
                }

                // $data = array('ret_code' => $ret_code);
                // echo json_encode($data);
                if (empty($retMsg)) {
                    $this->showJsonSuccess('注册成功');
                } else {
                    $this->showJsonError($retMsg);
                }
                return;
            }

        }

        if (arModule('Lib.User')->isLogin()) :
            $this->redirect('user');
        else :
            if (arRequest('action') !== 'logout') :
                $backUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
                if (arModule('wechat.Base')->isWeixin()) :
                    $backUrl = urlencode($backUrl);
                    // code 获取用户信息
                    $this->redirect(
                        arModule('wechat.Base')->loginUrl(array('back_url' => $backUrl))
                    );
                else :
                    if ($backUrl) :
                        arComp('list.session')->set('ar_back_url', $backUrl);
                    endif;
                endif;
            endif;
        endif;

        // 注册登录CODE 扫码登录
        $accode = arModule('Lib.Session')->register();
        $wechatshowPcLoginUrl = arU('/wechat/show/loginPc', array('accode' => $accode), 'FULL');
        $pcLoginCodeUrl = arModule('wechat.Base')->loginUrl(array('back_url' => urlencode($wechatshowPcLoginUrl)));
        $this->assign(array('pcLoginCodeUrl' => $pcLoginCodeUrl));
        $this->display();

    }

    //个人主页
    public function userAction(){
        $this -> isLogin();
        $this->assign(array('page_title' => '个人主页'));
        if (arPOST()) {
            // 过滤字符
            $utils = new Utils();
            $post = $utils::shtmlspecialchars(arPOST());
            if (isset($post['sign']) && $post['sign'] == 'editApply') { //var_dump($post);exit;
                # 申请的处理...['sign':'xxx', 'data':{'act':'agree','id':'xxx'}]
                // 获取原信息
                $condition = array('i_id' => $post['data']['aim'][0], 'type' => $post['data']['aim'][1], 'u_id' => $post['data']['aim'][2]);
                $applyOld = U_item_task_applyModel::model() -> getDb() -> where($condition) -> queryRow();
                // 获得项目的名称
                $itemName = U_itemsModel::model() -> getDb() -> select('i_name') -> where('id='.$applyOld['i_id']) -> queryRow();
                // 职位名称
                $jobName = U_department_jobsModel::model() -> getDb() -> select('j_name') -> where('id='.$applyOld['u_id']) -> queryRow();
                // 哪位用户
                $userName = U_usersModel::model() -> getDb() -> select('nickname') -> where('id='.$applyOld['u_id']) -> queryRow();
                if ($applyOld['flag'] == 0) {
                    # 同意/拒绝...
                    if ($post['data']['chose'] == '同意') { // 申请
                        # 同意...
                        $updateData = array('flag' => 1);
                        $content = '用户：'.$userName['nickname'].'成功加入'.$itemName['i_name'].'项目。参与'.$jobName['j_name'];
                        // 在任务表中添加一条记录
                        $insertData = array('i_id' => $applyOld['i_id'],'u_id' => $applyOld['u_id'],'type' => $applyOld['type'],'stay' =>1);
                        U_item_taskModel::model() -> getDb() -> insert($insertData);
                    }elseif($post['data']['chose'] == '拒绝'){
                        # 不同意
                        $updateData = array('flag' => 2);
                        $content = '用户：'.$userName['nickname'].'加入'.$itemName['i_name'].'项目，参与'.$jobName['j_name'].'申请失败!';
                    }else{
                        # 撤销
                        $data = 0; // 不能操作
                        echo json_encode($data);
                        exit;
                    }

                }elseif ($applyOld['flag'] == 1){ // 已同意
                    // 查询原来任务
                    $condition['stay'] = 1;
                    $taskOld = U_item_taskModel::model() -> getDb() -> where($condition) -> queryRow();
                    if ($post['data']['chose'] == '撤销') {
                        # 撤销 恢复成申请的状态
                        $updateData = array('flag' => 0);
                        $content = '用户：'.$userName['nickname'].'撤出'.$itemName['i_name'].'项目的'.$jobName['j_name'];
                        U_item_taskModel::model() -> getDb() -> where('id='.$taskOld['id']) -> update(array('stay' => 0));

                    }elseif ($post['data']['chose'] == '同意'){
                        # 同意
                        $data = 0; // 不能操作
                        echo json_encode($data);
                        exit;
                    }else{
                        # 不同意
                        $updateData = array('flag' => 2);
                        $content = '用户：'.$userName['nickname'].'离开'.$itemName['i_name'].'项目的'.$jobName['j_name'].'的工作。';
                    }

                }elseif($applyOld['flag'] == 2){ // 已拒绝
                    if ($post['data']['chose'] == '撤销') {
                        # 撤销 恢复成申请的状态
                        $updateData = array('flag' => 0);
                        $content = '用户：'.$userName['nickname'].'恢复'.$itemName['i_name'].'项目的'.$jobName['j_name'].'的申请。';

                    }elseif ($post['data']['chose'] == '拒绝'){
                        # 拒绝
                        $data = 0; // 不能操作
                        echo json_encode($data);
                        exit;
                    }else{
                        # 同意
                        $updateData = array('flag' => 1);
                        $content = '用户：'.$userName['nickname'].'成功加入'.$itemName['i_name'].'项目。参与'.$jobName['j_name'];
                        // 在任务表中添加一条记录
                        $insertData = array('i_id' => $applyOld['i_id'],'u_id' => $applyOld['u_id'],'type' => $applyOld['type'],'stay' =>1);
                        U_item_taskModel::model() -> getDb() -> insert($insertData);
                    }
                }

                $applyFlag = U_item_task_applyModel::model() -> getDb() -> where('id='.$applyOld['id']) -> update($updateData);
                if ($applyFlag) {
                    # 记录到进度表...
                    // '项目概述','项目变更','成员变更', '任务变更', '提成变更'
                    $type =  3;
                    $loginsert = array('content' => $content, 'type' => $type, 'u_id' => $_SESSION['u_id'], 'i_id' => $_SESSION['i_id'], 'Date' => date('Y-m-d H:i:s'));
                    $log = U_item_develop_logModel::model() -> getDb() -> insert($loginsert);
                    $data = 1; // success
                }else{
                    $data = 0; // false
                }

                echo json_encode($data);
                exit;
            }elseif (isset($post['sign']) && $post['sign'] == 'page') {
                // 分页数据返回
                $total = U_item_task_applyModel::model() -> getDb() -> count();
                $min = 0;
                $max = ceil($total/5);
                $num = preg_replace('/page/', '', $post['data']) +1; // 获取是第几页
                // if ($num <= $min) {
                //     $limit = 0; // 分页的起始位置
                // }elseif ($num > $max) {
                //     $limit = ($max -1 ) * 5; // 分页的起始位置
                // }else{
                //     $limit = ($num -1 ) * 5; // 分页的起始位置
                // }
                // $allApplySql = 'SELECT a.*, u.nickname, i.i_name, j.j_name FROM u_item_task_apply AS a, u_users AS u, u_items as i, u_department_jobs as j  WHERE j.id=a.type AND u.id=a.u_id AND i.id=a.i_id ORDER BY a.i_id DESC limit '.$limit.',5';
                // $allApply = U_item_task_applyModel::model() -> getDb() -> sqlQuery($allApplySql);

                if ($num <= $min) {
                    $allApply = null;
                }elseif ($num > $max) {
                    $allApply = null;
                }else{
                    $limit = ($num -1 ) * 5; // 分页的起始位置
                    $allApplySql = 'SELECT a.*, u.nickname, i.i_name, j.j_name FROM u_item_task_apply AS a, u_users AS u, u_items as i, u_department_jobs as j  WHERE j.id=a.type AND u.id=a.u_id AND i.id=a.i_id ORDER BY a.i_id DESC limit '.$limit.',5';
                    $allApply = U_item_task_applyModel::model() -> getDb() -> sqlQuery($allApplySql);
                }


                // foreach ($allApply as $key => &$value) {
                //     if ($value['flag'] == 0) {
                //         $value['flag'] = '申请';
                //     }elseif ($value['flag'] == 1) {
                //         $value['flag'] = '成功';
                //     }elseif ($value['flag'] == 0) {
                //         $value['flag'] = '拒绝';
                //     }
                // }
                echo json_encode($allApply);
                exit;
            }
        }
        // 查询个人信息
        $userInf = U_usersModel::model() -> getDb() -> where('id='.$_SESSION['u_id']) -> queryRow();
        // 所在部门及所在职位
        if ($userInf['department']) {
            $userInf['department'] = explode(',', $userInf['department']);
            $userDepartments = U_departmentModel::model()
                -> getDb()
                -> select('id, d_name')
                -> where(array('id' => $userInf['department']))
                -> queryAll();

            // var_dump($userInf['department']);exit();
            foreach ($userDepartments as $key => &$value) {
                $userDerJobs = U_department_jobsModel::model() -> getDb() -> select('id, j_name') -> where('j_did ='.$value['id']) -> queryAll();
                // var_dump($userDerJobs);exit();
                foreach ($userDerJobs as $k => $v) {
                    if (preg_match('/'.$v['id'].'/', $userInf['job'])) {
                        $value['jobs'][] = $v;
                    }
                }
            }
        }else{
            $userDepartments = array();
        }

        // 参与项目
        $inItems = U_item_taskModel::model() -> getDb() -> select('i_id') -> where('u_id='.$_SESSION['u_id'].' and stay=1') -> group('i_id') -> order('i_id desc') -> queryAll();

        foreach ($inItems as $key => &$value) {
            # 查询出项目名称...
            $i_name = U_itemsModel::model() -> getDb() -> select('money, i_name') -> where('id='.$value['i_id'].' and online=1') -> queryRow();
            // var_dump($i_name);
            if (!$i_name) {
                unset($inItems[$key]);
            }else{
                $value['i_name'] = $i_name['i_name'];
                $value['money'] = $i_name['money'];
                // 查询该项目的具体任务
                $tsakSql = 'SELECT t.type, j_name, duty, percent FROM u_department_jobs as j, u_item_task as t where t.type=j.id AND j.i_id='.$value['i_id'].' and u_id='.$_SESSION['u_id'].' and stay=1';
                $db = U_itemsModel::model() -> getDb();
                $value['task'] = $db -> sqlQuery($tsakSql);
            }

        }

        // 申请项目 所有 针对管理员的
        // $allApply = U_item_task_applyModel::model() -> getDb() -> queryAll();
        $allApplySql = 'SELECT a.*, u.nickname, i.i_name, j.j_name FROM u_item_task_apply AS a, u_users AS u, u_items as i, u_department_jobs as j  WHERE j.id=a.type AND u.id=a.u_id AND i.id=a.i_id ORDER BY a.i_id DESC limit 0,5';
        $allApply = U_item_task_applyModel::model() -> getDb() -> sqlQuery($allApplySql);

        // 申请项目 所有 针对管理员的 含分页

        // $total = U_item_task_applyModel::model() -> getDb() -> count();
        // $per = 8;
        // $pageOne = new pageOne($total,$per);
        // $sql = 'SELECT a.*, u.nickname, i.i_name, j.j_name FROM u_item_task_apply AS a, u_users AS u, u_items as i, u_department_jobs as j  WHERE j.id=a.type AND u.id=a.u_id AND i.id=a.i_id ORDER BY a.i_id DESC '.$pageOne -> limit;
        // $pagelist = $pageOne ->fpage();
        // $allApply = U_item_task_applyModel::model() -> getDb() -> sqlQuery($sql);


        // $this -> assign(array('userInf' => $userInf, 'userDepartments' => $userDepartments, 'inItems' => $inItems, 'allApply' => $allApply, 'pagelist' => $pagelist ));
        $this -> assign(array('userInf' => $userInf, 'userDepartments' => $userDepartments, 'inItems' => $inItems, 'allApply' => $allApply ));
        $this->display();

    }

    //个人主页
    public function myuserAction(){
        $this -> isLogin();
        // 查询个人信息
        $userInf = U_usersModel::model() -> getDb() -> where('id='.$_SESSION['u_id']) -> queryRow();
        // 所在部门及所在职位
        if ($userInf['department']) {
            $userDepartments = U_departmentModel::model() -> getDb() -> select('id, d_name') -> where('id in('.$userInf['department'].')') -> queryAll();
            // var_dump($userInf['department']);exit();
            foreach ($userDepartments as $key => &$value) {
                $userDerJobs = U_department_jobsModel::model() -> getDb() -> select('id, j_name') -> where('j_did ='.$value['id']) -> queryAll();
                // var_dump($userDerJobs);exit();
                foreach ($userDerJobs as $k => $v) {
                    if (preg_match('/'.$v['id'].'/', $userInf['job'])) {
                        $value['jobs'][] = $v;
                    }
                }
            }
        }else{
            $userDepartments = array();
        }

        // 参与项目
        $inItems = U_item_taskModel::model() -> getDb() -> select('i_id') -> where('u_id='.$_SESSION['u_id'].' and stay=1') -> group('i_id') -> order('i_id desc') -> queryAll();

        // 测试没有参与项目
        // $inItems = U_item_taskModel::model() -> getDb() -> select('i_id') -> where('u_id=18 and stay=1') -> group('i_id') -> order('i_id desc') -> queryAll();

        foreach ($inItems as $key => &$value) {
            # 查询出项目名称...
            $i_name = U_itemsModel::model() -> getDb() -> select('money, i_name') -> where('id='.$value['i_id'].' and online=1') -> queryRow();
            // var_dump($i_name);
            if (!$i_name) {
                unset($inItems[$key]);
            }else{
                $value['i_name'] = $i_name['i_name'];
                $value['money'] = $i_name['money'];
                // 查询该项目的具体任务
                $tsakSql = 'SELECT t.type, j_name, duty, percent FROM u_department_jobs as j, u_item_task as t where t.type=j.id AND i_id='.$value['i_id'].' and u_id='.$_SESSION['u_id'].' and stay=1';
                $db = U_itemsModel::model() -> getDb();
                $value['task'] = $db -> sqlQuery($tsakSql);
            }

        }

        $this -> assign(array('userInf' => $userInf, 'userDepartments' => $userDepartments, 'inItems' => $inItems));
        $this->display();

    }

    // 项目发布
    public function pmAction()
    {
        $this -> isLogin();
        $this->assign(array('page_title' => '项目管理'));
        if (arPOST()) {
            // 过滤数据
            $utils = new Utils();
            $post = $utils::shtmlspecialchars(arPOST());

            if(isset($post['sign']) && $post['sign'] == 'item'){

                // 项目发布
                $data = array();// 要还回的数据

                // 数据处理 1. 项目状态 2. 开发周期
                switch ($post['status']) {
                    case "未审核":
                        $post['status'] = "1";
                        break;
                    case "审核":
                        $post['status'] = "2";
                        break;
                    case "分析":
                        $post['status'] = "3";
                        break;
                    case "组队":
                        $post['status'] = "4";
                        break;
                    case "开发":
                        $post['status'] = "5";
                        break;
                    case "完成":
                        $post['status'] = "6";
                        break;
                }

                if (preg_match('/天/', $post['days'])) {
                    $post['days'] = preg_replace('/天/', 'd', $post['days']);

                }elseif (preg_match('/周/', $post['days'])) {
                    $post['days'] = preg_replace('/周/', 'w', $post['days']);

                }elseif (preg_match('/月/', $post['days'])) {
                    $post['days'] = preg_replace('/月/', 'm', $post['days']);

                }elseif (preg_match('/年/', $post['days'])) {
                    $post['days'] = preg_replace('/年/', 'y', $post['days']);

                }

                $iteminsert = array('i_name' => $post['i_name'], 'money' => $post['money'], 'contractDate' => $post['contractDate'], 'days' => $post['days'], 'requirement' => $post['requirement'], 'status' => $post['status'], 'publisher' => $_SESSION['u_id'], 'releaseDate' => date('Y-m-d H:i:s'), 'online' => 1 );

                $flag = U_itemsModel::model() -> getDb() -> insert($iteminsert);

                // 记录进度
                if ($flag) {

                    // 生成二维码图片并保存
                    include '/main/Ext/QRcode.class.php';
                    $value = arComp('url.route')->host().'/Index/pd_item/id/'.$flag; //二维码内容
                    $errorCorrectionLevel = 'L';//容错级别
                    $matrixPointSize = 7.5;//生成图片大小
                    //  生成二维码图片
                    QRcode::png($value, 'Upload/item'.$flag.'.png', $errorCorrectionLevel, $matrixPointSize, 2);
                    U_itemsModel::model() -> getDb() -> where('id='.$flag) -> update(array('img' => 'item'.$flag.'.png' ));

                    // 添加到 item_develop_log 进度表
                    $content = '发布项目<b>'.$_POST['i_name'].'</b>成功!';
                    // '项目概述','项目变更','成员变更'
                    $type =  1;
                    $loginsert = array('content' => $content, 'type' => $type, 'u_id' => $_SESSION['u_id'], 'i_id' => $flag, 'Date' => date('Y-m-d H:i:s'));

                    $log = U_item_develop_logModel::model() -> getDb() -> insert($loginsert);
                    if ($log) {
                         $id = $flag;
                    }

                }else{
                    $id = 0;
                }

                $data['id'] = $id;
                echo json_encode($data);
                exit;

            }elseif (isset($post['sign']) && $post['sign'] == 'delLists') {
                // 删除项目
                $delIds = arPOST('data');
                // var_dump($delIds);exit;
                foreach ($delIds as $value) {

                    $data = 0;
                    if ($this -> delItem($value)) {
                        $data = 1;
                    }

                }

                echo json_encode($data);
                exit;

            }elseif (isset($post['sign']) && $post['sign'] == 'recovery') {
                // 恢复某个项目
                $recoverylIds = arPOST('data');
                foreach ($recoverylIds as $key => $value) {

                    $data = 0;
                    if ($this -> recoveryItem($value)) {
                        $data = 1;
                    }

                }

                echo json_encode($data);
                exit;
            }

        }

        // 发布的所有在线项目 循环查询
        $itemsOn = U_itemsModel::model() -> getDb() -> select('id, i_name, status, img, users') -> where('publisher = '.$_SESSION['u_id'].' and online=1') -> order('id desc') -> queryAll();

        foreach ($itemsOn as $key => &$value) {
            // 将项目状态由数字转换成中文
            $status = U_item_status_typeModel::model() -> getDb() -> select('name') -> where('id = '.$value['status']) -> queryRow();
            $value['statusN'] = $status['name'];

            // 参与人员的数目
            if ($value['users']) {
                $arr = explode(',', $value['users']);
                $value['usersNum'] = count($arr);
            }else{
                $value['usersNum'] = 0;
            }

        }

        // 发布的所有停用项目 循环查询
        $itemsOff = U_itemsModel::model() -> getDb() -> select('id, i_name, status, img, users') -> where('publisher = '.$_SESSION['u_id'].' and online=0') -> order('id desc') -> queryAll();

        foreach ($itemsOff as $key => &$value) {
            // 将项目状态由数字转换成中文
            $status = U_item_status_typeModel::model() -> getDb() -> select('name') -> where('id = '.$value['status']) -> queryRow();
            $value['statusN'] = $status['name'];

            // 参与人员的数目
            if ($value['users']) {
                $arr = explode(',', $value['users']);
                $value['usersNum'] = count($arr);
            }else{
                $value['usersNum'] = 0;
            }

        }

        // 发布的所有项目 用联表查询
        // $itemsSql = 'select i.id, i_name, name as statusN, status, money, days from u_items as i,u_item_status_type as s where i.status=s.id and i.publisher='.$_SESSION['u_id'] .' order by i.id desc';
        // $items = U_itemsModel::model() -> getDb() -> sqlQuery($itemsSql);
        // echo '<pre>';
        // var_dump($itemsOff);exit;
        $this -> assign(array('itemsOn' => $itemsOn, 'itemsOff' => $itemsOff));
        $this->display();

    }

    // 部门管理
    public function dmAction()
    {
        $this -> isLogin();
        if (arPOST()) {
            // 过滤字符
            $utils = new Utils();
            $post = $utils::shtmlspecialchars(arPOST());
            if (isset($post['sign']) && $post['sign'] == 'addDepartment') {
                # 添加部门...
                // 查询原来数据
                $departmentOld = U_departmentModel::model() -> getDb() -> queryAll();
                // 判断要添加的部门存在么？
                $data = 0;
                foreach ($departmentOld as $key => $value) {
                    if ($value['d_name'] == $post['d_name']) {
                        $data = 2; // 该部门已存在
                        break;
                    }
                }

                // 数据插入到数据库中
                if ($data != 2) {
                    # add new department...
                    $flag = U_departmentModel::model() ->getDb() ->insert(array('d_name' => $post['d_name']));
                    if ($flag) {
                        # add new jobs...
                        foreach ($post['staff'] as $key => $value) {
                            $flageJob = U_department_jobsModel::model() -> getDb() -> insert(array('j_name' => $value['j_name'], 'j_did' => $flag ));
                            if (isset($value['users'])) {
                                # user to add department and jobs...
                                foreach ($value['users'] as $k => $v) {
                                    // 获取原来的数据
                                    $userInf = U_usersModel::model() -> getDb() -> select('department, job') -> where('id='.$v) -> queryRow();
                                    $updateData = array('department' =>$userInf['department'].','.$flag , 'job' => $userInf['department'].','.$flageJob );
                                    U_usersModel::model() -> getDb() -> where('id='.$v) -> update($updateData);
                                }

                            }
                            $data = 1;

                        }

                    }

                }
                echo json_encode($data);
                exit;

            }elseif (isset($post['sign']) && $post['sign'] == 'addUsers') {
                # 还回所有user...
                $users = U_usersModel::model() -> getDb() -> select('id, nickname') -> queryAll();
                $data = $users;
                echo json_encode($data);
                exit;
            }elseif (isset($post['sign']) && $post['sign'] == 'update') {
                # edit department...
                // var_dump($post['data']);exit();
                $dataNew = $post['data'];
                // var_dump($dataNew);exit;
                foreach ($dataNew as $key => $value) {
                    $data = 0;
                    if (isset($value['sign'])) {
                        # del department ...
                        // 1.在 department 中删除
                        $flag = U_departmentModel::model() -> getDb() -> where('id='.$value['d_id']) -> delete();
                        if ($flag) {
                            // 2. 在 jobs 中删除其下所有的职务
                            $jobId = U_department_jobsModel::model() -> getDb() -> select('id') -> where('j_did='.$value['d_id']) -> queryAll();
                            if ($jobId) {
                                # 存在职务
                                $flagJob = U_department_jobsModel::model() -> getDb() -> where('j_did='.$value['d_id']) -> delete();
                                if ($flagJob) {
                                    // 3.找出该部门的所有用户，并修改 department 和 job 字段
                                    $usersInf = U_usersModel::model() -> getDb() -> select('id, department, job') -> where('department like "%'.$value['d_id'].'%"') -> queryAll();
                                    if ($usersInf) {
                                        # 存在用户...
                                        foreach ($usersInf as $k => &$v) {
                                            # edit field of department...
                                            $data = 0;
                                            $departments = explode(',', $v['department']);
                                            foreach ($departments as $kk => $vv) {
                                                if ($vv == $value['d_id']) {
                                                    unset($departments[$kk]);
                                                }
                                            }
                                            $v['department'] = implode(',', $departments);

                                            # edit field of job...
                                            $jobs = explode(',', $v['job']);
                                            # 职务的数据处理
                                            foreach ($jobId as $vvv) {
                                                $arr[] = $vvv['id'];
                                            }

                                            foreach ($jobs as $kk => $vv) {
                                                if (in_array($vv, $arr)) {
                                                    unset($jobs[$kk]);
                                                }
                                            }
                                            $v['job'] = implode(',', $jobs); // var_dump($jobId);exit;

                                            // 数据库中修改
                                            $flagUser = U_usersModel::model() -> getDb() -> where('id='.$v['id']) -> update(array('department' => $v['department'], 'job' => $v['job'] ));
                                            if ($flagUser) {
                                                $data = 1;
                                            }

                                        }

                                    }else{
                                        $data = 1;
                                    }

                                }

                            }else{
                                $data = 1;
                            }

                        }

                    }else{
                        # 部门操作
                        // 部门名称是否修改
                        $departmentName = U_departmentModel::model() -> getDb() -> select('d_name') -> where('id='.$value['d_id']) -> queryRow();
                        if(isset($value['d_name']) && $value['d_name'] && $departmentName['d_name'] != $value['d_name']){
                            # update
                            U_departmentModel::model() -> getDb() -> where('id='.$value['d_id']) -> update(array('d_name' => $value['d_name'] ));
                        }

                        // 该部门的职位操作
                        if (isset($value['jobs'])) {
                            #  增， 删， 改...
                            foreach ($value['jobs'] as $k => $v) {
                                $data = 0;
                                if (isset($v['sign'])) {
                                    # 删职位...
                                    $flagDelJob = U_department_jobsModel::model() -> getDb() -> where('id='.$v['j_id']) -> delete();
                                    if ($flagDelJob) {
                                        # 查询该职位的所有用户...
                                        $usersInf = U_usersModel::model() -> getDb() -> select('id, department, job') -> where('job like "%'.$v['j_id'].'%"') -> queryAll();
                                        # 查询该部门的其他职位
                                        $otherJobs = U_department_jobsModel::model() -> getDb() -> select('id') -> where('j_did='.$value['d_id']) -> queryAll();
                                        $arr = array();
                                        foreach ($otherJobs as $jk) {
                                            $arr[] = $jk['id'];
                                        }
                                        # 修改 department 和 job
                                        foreach ($usersInf as $uk => $uv) {
                                            # job
                                            $jobs = explode(',', $uv['job']);
                                            $departments = explode(',', $uv['department']);
                                            foreach ($jobs as $dk => $dv) {
                                                if ($dv == $v['j_id']) {
                                                    unset($jobs[$dk]);
                                                }
                                            }

                                            if (isset($jobs)) {
                                                $num = 0;
                                                foreach ($jobs as $dk => $dv) {
                                                    if (in_array($dv, $arr)) {
                                                        $num = 1;
                                                        break;
                                                    }
                                                }
                                                if (!$num) {
                                                    # 修改 department 删除...
                                                    foreach ($departments as $jk => $jv) {
                                                        if ($jv == $value['d_id']) {
                                                            unset($departments[$jk]);
                                                        }
                                                    }
                                                }

                                            }else{
                                                # 修改 department 删除
                                                foreach ($departments as $jk => $jv) {
                                                    if ($jv == $value['d_id']) {
                                                        unset($departments[$jk]);
                                                    }
                                                }

                                            }

                                            $jobData = implode(',', $jobs);
                                            $depData = implode(',', $departments);
                                            U_usersModel::model() -> getDb() -> where('id='.$uv['id']) -> update(array('department' => $depData, 'job' => $jobData ));

                                        }
                                    }
                                    $data = 1;
                                }else{

                                    if (strpos($v['j_id'], 'new') === false) {
                                        # 改职位...
                                        // 改职位名称
                                        // echo $v['j_id'];
                                        $jobName = U_department_jobsModel::model() -> getDb() -> select('j_name') -> where('id='.$v['j_id']) -> queryRow();
                                        if($jobName['j_name'] != $v['j_name']){
                                            # update
                                            U_department_jobsModel::model() -> getDb() -> where('id='.$v['j_id']) -> update(array('j_name' => $v['j_name'] ));
                                        }
                                        // echo $jobName['j_name'];exit;
                                        // 对该职位下的人员操作
                                        if (!isset($v['users'])) {
                                            $v['users'] = array();
                                        }

                                        # 原始数据
                                        $users = U_usersModel::model() -> getDb() -> select('id') -> where('job like "%'.$v['j_id'].'%"') -> queryAll();
                                        $arrKeep = array();
                                        $arrAdd = array();
                                        $arrDel = array();
                                        $arr = array();
                                        // 分析数据
                                        foreach ($users as $uk => $uv) {
                                            if (in_array($uv['id'], $v['users'])) {
                                                $arrKeep[] = $uv['id'];
                                            }else{
                                                $arrDel[] = $uv['id'];
                                            }
                                            $arr[] = $uv['id'];
                                        }
                                        foreach ($v['users'] as $uk => $uv) {
                                            if (!in_array($uv, $arr)) {
                                                $arrAdd[] = $uv;
                                            }
                                        }

                                        # 查询该部门的其他职位
                                        $otherJobs = U_department_jobsModel::model() -> getDb() -> select('id') -> where('j_did='.$value['d_id']) -> queryAll();
                                        $arrJob = array();
                                        foreach ($otherJobs as $jk) {
                                            $arrJob[] = $jk['id'];
                                        }
                                        // echo '<pre>keep';
                                        // var_dump($arrKeep);
                                        // echo 'del';
                                        // var_dump($arrDel);
                                        // echo 'add';
                                        // echo var_dump($arrAdd);
                                        // exit;
                                        # arrDel
                                        foreach ($arrDel as $adv) {
                                            # 查询该用户信息...
                                            $userInf = U_usersModel::model() -> getDb() -> select('department, job') -> where('id='.$adv) -> queryRow();
                                            $jobs = explode(',', $userInf['job']);
                                            $deps = explode(',', $userInf['department']);
                                            foreach ($jobs as $jk => $jv) {
                                                if ($jv == $v['j_id']) {
                                                    unset($jobs[$jk]);
                                                }
                                            }

                                            if (isset($jobs)) {
                                                $kk = 0;
                                                foreach ($jobs as $jv) {
                                                    if (in_array($jv, $arrJob)) {
                                                        $kk = 1;
                                                        break;
                                                    }
                                                }

                                                if (!$kk) {
                                                    foreach ($deps as $dk => $dv) {
                                                        if ($dv == $value['d_id']) {
                                                            unset($deps[$dk]);
                                                        }
                                                    }
                                                }

                                            }else{
                                                foreach ($deps as $dk => $dv) {
                                                    if ($dv == $value['d_id']) {
                                                        unset($deps[$dk]);
                                                    }
                                                }

                                            }

                                            $jobData = implode(',', $jobs);
                                            $depsData = implode(',', $deps);
                                            U_usersModel::model() -> getDb() -> where('id='.$adv) -> update(array('department' => $depsData, 'job' => $jobData ));

                                        }

                                        # arrAdd
                                        foreach ($arrAdd as $av) {
                                            # 查询该用户信息...
                                            $userInf = U_usersModel::model() -> getDb() -> select('department, job') -> where('id='.$av) -> queryRow();
                                            $jobs = explode(',', $userInf['job']);
                                            $kk = 0;
                                            foreach ($jobs as $jk => $jv) {
                                                if (in_array($jv, $arrJob)) {
                                                    $kk = 1;
                                                    break;
                                                }
                                            }

                                            if (!$kk) {
                                                $userInf['department'] .= ','.$value['d_id'];
                                            }

                                            $userInf['job'] .= ','.$v['j_id'];
                                            U_usersModel::model() -> getDb() -> where('id='.$av) -> update(array('department' => $userInf['department'], 'job' => $userInf['job'] ));
                                        }

                                        $data = 1;

                                    }else{
                                        # 增职位
                                        $jobNames = U_department_jobsModel::model() -> getDb() -> select('j_name') -> where('j_did='.$value['d_id']) -> queryAll();
                                        $aa = 0;
                                        foreach ($jobNames as $kjn => $vjn) {
                                            if ($v['j_name'] == $vjn['j_name']) {
                                                $aa = 1;
                                                break;
                                            }
                                        }
                                        if (!$aa) {
                                            $flag = U_department_jobsModel::model() -> getDb() ->insert(array('j_name' => $v['j_name'], 'j_did' => $value['d_id'] ));
                                            if (isset($v['users'])) {
                                                # 查询该部门的其他职位
                                                $otherJobs = U_department_jobsModel::model() -> getDb() -> select('id') -> where('j_did='.$value['d_id']) -> queryAll();
                                                $arrJob = array();
                                                foreach ($otherJobs as $jk) {
                                                    $arrJob[] = $jk['id'];
                                                }

                                                # 查询将要添加的人员信息
                                                $userStr = implode(',', $v['users']);
                                                $users = U_usersModel::model() -> getDb() -> select('id, department, job') -> where('id in('.$userStr.')') -> queryAll();
                                                foreach ($users as $uk => $uv) {
                                                    $jobs = explode(',', $uv['job']);
                                                    $kk = 0;
                                                    foreach ($jobs as $jk => $jv) {
                                                        if (in_array($jv, $arrJob)) {
                                                            $kk = 1;
                                                            break;
                                                        }
                                                    }

                                                    if (!$kk) {
                                                        if ($uv['department']) {
                                                            $uv['department'] .= ','.$value['d_id'];
                                                        }else{
                                                            $uv['department'] .= $value['d_id'];
                                                        }

                                                    }
                                                    if ($uv['job']) {
                                                        $uv['job'] .= ','.$flag;
                                                    }else{
                                                        $uv['job'] .= $flag;
                                                    }

                                                    U_usersModel::model() -> getDb() -> where('id='.$uv['id']) -> update(array('department' => $uv['department'], 'job' => $uv['job'] ));
                                                }
                                                $data = 1;

                                            }
                                        }
                                        $data = 1;
                                    }

                                }
                                $data =1;
                            }

                        }
                        $data = 1;
                    }

                }
                echo json_encode($data);
                exit;

            }elseif (isset($post['sign']) && $post['sign'] == 'users') {
                # 还回所有用户...
                $users = U_usersModel::model() -> getDb() -> select('id, nickname, tel, weixin, qq, email') -> queryAll();
                echo json_encode($users);
                exit;
            }


        }
        // 查询各部门的成员
        $depJobUsers = U_departmentModel::model() -> getDb() -> select('id, d_name') -> queryAll();
        foreach ($depJobUsers as $k => &$value) {
            # 职位...
            $value['staff'] = U_department_jobsModel::model() -> getDb() -> select('id, j_name') -> where('j_did='.$value['id']) ->queryAll();
            foreach ($value['staff'] as $key => &$valueJob) {
                # 成员...
                // 1. 模糊查询
                $mohu = U_usersModel::model() -> getDb() -> select('id, nickname, job, tel, weixin, qq, email') -> where('job like "%'.$valueJob['id'].'%"') -> queryAll();
                // 2, 筛选
                foreach ($mohu as $key => $mvalue) {
                    $jobs = explode(',', $mvalue['job']);
                    if (in_array($valueJob['id'], $jobs)) {
                        $valueJob['users'][] = $mvalue;
                    }
                }

            }
        }
        // foreach ($depJobUsers as $k => &$value) {

        //     $value['users'] = U_usersModel::model() -> getDb() -> select('id, nickname') -> where('department like "%'.$value['id'].'%"') -> queryAll();
        // }
        // echo '<pre>';
        // var_dump($depJobUsers);
        // echo json_encode($depJobUsers);
        // exit;
        $this -> assign(array('depJobUsers' => $depJobUsers ));
        $this->display();

    }

    // 个人管理
    public function umAction()
    {
        $this -> isLogin();
        $this->assign(array('page_title' => '个人中心'));
        if (arPOST()) {
            // 过滤字符
            $utils = new Utils();
            $post = $utils::shtmlspecialchars(arPOST());
            if (isset($post['sign']) && $post['sign'] == 'editPassword') {
                # 修改密码...
                // 查询旧密码  oldPwd,newPwd,newPwd2
                $passwordOld = U_usersModel::model() -> getDb() -> select('password') -> where('id='.$_SESSION['u_id']) -> queryRow();
                if ($passwordOld['password'] == $this->pwd($post['oldPwd'])) {

                    if ($post['newPwd'] === $post['newPwd2']) {

                        if ($passwordOld['password'] == $this->pwd($post['newPwd'])) {
                            $data = 5; // 新旧密码一致

                        }else{
                            if (preg_match('/^\w{6,16}$/', $post['newPwd'])) {
                                # code...
                                $updateData = array('password' => $this-> pwd($post['newPwd']) );
                                $flag = U_usersModel::model() -> getDb() -> where('id='.$_SESSION['u_id']) -> update($updateData);
                                $data = $flag?1:0; // 0--修改失败 1--修改成功

                            }else{
                                $data = 2; // 密码格式不正确

                            }

                        }

                    }else{
                        $data = 4; // 两次密码不一致

                    }

                }else{
                    $data = 3; // 旧密码错误

                }

                echo json_encode($data);
                exit;

            }

        }
        // 查询个人资料信息
        $userInf = U_usersModel::model() -> getDb() -> select('nickname, photo, qq, email, weixin') -> where('id='.$_SESSION['u_id']) -> queryRow();
        // $userInf = U_usersModel::model() -> getDb() -> select('nickname, photo, qq, email, weixin') -> where('id=14') -> queryRow();
        $this -> assign(array('userInf' => $userInf));
        $this->display();

    }

    public function myumAction()
    {
        $this -> isLogin();
        // 查询个人资料信息
        $userInf = U_usersModel::model() -> getDb() -> select('nickname, photo, qq, email, weixin') -> where('id='.$_SESSION['u_id']) -> queryRow();
        // $userInf = U_usersModel::model() -> getDb() -> select('nickname, photo, qq, email, weixin') -> where('id=14') -> queryRow();
        $this -> assign(array('userInf' => $userInf));
        $this->display();

    }
//--------------------------------------------------------------
    // 我的项目控制器
    public function um_projectAction()
    {
        $this -> isLogin();
        $this->assign(array('page_title' => '我的项目'));
        $this->display();

    }

    // 项目详情控制器
    public function um_project_infoAction()
    {
        $this -> isLogin();

        $this->display();
    }

    // 电话，微信，邮箱绑定控制器
    public function um_binding_infoAction()
    {
        // $this->setLayoutFile('');
        $this -> isLogin();
        $this->display('um_binding_info');
    }

    // 我的任务控制器
    public function um_my_taskAction(){
        $this -> display();
    }

    // 任务详情控制器
    public function um_my_taskinfoAction(){
        $this -> display();
    }
//----------------------------------------------------------------------
    public function pd_indexAction()
    {
        $this -> isLogin();
        $this->display();
    }

    // 项目详情
    public function pd_itemAction()
    {
        $this -> isLogin();
        $this->assign(array('page_title' => '项目详情'));
        if(arPOST()){
            $utils = new Utils();
            $post = $utils::shtmlspecialchars(arPOST());
            // $post = arPOST();
            if (isset($post['sign']) && $post['sign'] === 'updateItem') { //var_dump($post);exit;
                # 项目的修改
                $requirement = preg_replace('/\\\\/', '', arPOST('requirement'));
                // 是否有img标签
                if (preg_match('/<img.+?>/', $requirement) ) {
                    $post['requirement'] = preg_replace('/http:\/\/.+?Upload\//', "", $requirement);
                }else{
                    $post['requirement'] = $requirement;
                }

                $data = array(); // 要还回的数据
                // 数据处理 1. 项目状态 2. 开发周期
                switch ($post['status']) {
                    case "未审核":
                        $post['status'] = "1";
                        break;
                    case "审核":
                        $post['status'] = "2";
                        break;
                    case "分析":
                        $post['status'] = "3";
                        break;
                    case "组队":
                        $post['status'] = "4";
                        break;
                    case "开发":
                        $post['status'] = "5";
                        break;
                    case "完成":
                        $post['status'] = "6";
                        break;
                }

                if (preg_match('/天/', $post['days'])) {
                    $post['days'] = preg_replace('/天/', 'd', $post['days']);

                }elseif (preg_match('/周/', $post['days'])) {
                    $post['days'] = preg_replace('/周/', 'w', $post['days']);

                }elseif (preg_match('/月/', $post['days'])) {
                    $post['days'] = preg_replace('/月/', 'm', $post['days']);

                }elseif (preg_match('/年/', $post['days'])) {
                    $post['days'] = preg_replace('/年/', 'y', $post['days']);

                }

                // 获取原来的数据
                $itemOld = U_itemsModel::model() -> getDb() -> where('id = '.$_SESSION['i_id']) -> queryRow();
                // 更新数据准备

                $dataUpdate = array('money' => $post['money'], 'i_name' => $post['i_name'], 'status' => $post['status'], 'requirement' => $post['requirement'], 'contractDate' => $post['contractDate'], 'days' => $post['days'] );
                // 更新 还回受影响的条数
                $flag = U_itemsModel::model() -> getDb() -> where('id ='.$_SESSION['i_id']) -> update($dataUpdate);
                if ($flag) {
                    # 更新成功 向项目进度中写入信息
                    // 获取新的数据
                    $itemNew = U_itemsModel::model() -> getDb() -> where('id = '.$_SESSION['i_id']) -> queryRow();
                    $str = ''; // 记录变化
                    foreach ($itemOld as $key => $value) {
                        if ($itemOld[$key] !== $itemNew[$key]) {
                            switch ($key) {
                                case 'i_name':
                                    $keyC = '项目名称';
                                    break;
                                case 'money':
                                    $keyC = '金额';
                                    break;
                                case 'contractDate':
                                    $keyC = '合同日期';
                                    break;
                                case 'requirement':
                                    $keyC = '项目需求';
                                    break;
                                case 'status':
                                    $keyC = '项目状态';
                                    break;
                                case 'days':
                                    $keyC = '开发周期';
                                    break;

                                default:
                                    $keyC = '其他';
                                    break;
                            }
                            if ($keyC == '项目需求' ) {
                                $str .= $keyC.'变化。';
                            }else{
                                $str .= $keyC.'变化,由'.$itemOld[$key].'变成'.$itemNew[$key].'。';
                            }
                            // $str .= $keyC.'变化,由'.$itemOld[$key].'变成'.$itemNew[$key].'。\n';

                        }

                    }
                    // '项目概述','项目变更','成员变更','任务变更','提成变更'
                    $type =  2;
                    $loginsert = array('content' => $str, 'type' => $type, 'u_id' => $_SESSION['u_id'], 'i_id' => $_SESSION['i_id'], 'Date' => date('Y-m-d H:i:s'));
                    // 向数据库写入进度
                    $log = U_item_develop_logModel::model() -> getDb() -> insert($loginsert);
                    if ($log) {
                         $id = $flag;
                    }else{
                        $id = 0;
                    }

                }else{
                   $id = 0;

                }
                $data = $id;
                echo json_encode($data);
                exit;
            }elseif (isset($post['sign']) && $post['sign'] === 'department') {
                # 部门数据...
                echo json_encode($this -> department());
                exit;

            }elseif (isset($post['sign']) && $post['sign'] === 'jobs') {
                # 职务数据...
                echo json_encode($this -> jobs(arPOST('id')));
                exit;

            }elseif (isset($post['sign']) && $post['sign'] === 'users') {
                # 成员数据...
                echo json_encode($this -> users(arPOST('id')));
                exit;

            }elseif (isset($post['sign']) && $post['sign'] === 'loadData') {
                // 数据准备
                $data = array();
                $db = U_item_taskModel::model() -> getDb();
                $taskJobsSql = 'select t.type as id, j_name from u_item_task as t, u_department_jobs as j where t.type=j.id and i_id='.$_SESSION['i_id'].' and t.stay=1 group by t.type order by t.type desc';
                // $taskJobsSql = 'select t.type as id, j_name from u_item_task as t, u_department_jobs as j where t.type=j.id and i_id=18 and t.stay=1 group by t.type order by t.type desc';
                $taskJobs = $db -> sqlQuery($taskJobsSql);
                foreach ($taskJobs as $key => &$value) {
                    // 职务所占比例
                    $jobPerSql = 'select percent from u_item_comission where i_id ='.$_SESSION['i_id'].' and keep=1 and type ='.$value['id'];
                    // $jobPerSql = 'select percent from u_item_comission where i_id ='.$_SESSION['i_id'].' and type =1';
                    $jobPer = $db -> sqlQuery($jobPerSql);
                    if (!empty($jobPer)) {
                        $value['percent'] = $jobPer[0]['percent'];
                    }else{
                        $value['percent'] = '';
                    }

                    // 队员
                    $memberSql = 'select t.u_id as id, duty, t.percent, u.nickname, u.tel, u.qq, u.email, u.weixin  from u_item_task as t, u_users as u where u.id=t.u_id and t.type ='.$value['id'].' and i_id='.$_SESSION['i_id'].' and t.stay=1 order by t.type desc,t.id desc';
                    $member = $db -> sqlQuery($memberSql);
                    $value['man'] = $member;
                    $data[] = $value;
                    // $data[$value['j_name']] = $value;
                }
                // $data = $taskJobs;
                echo json_encode($data);
                exit;

            }elseif (isset($post['sign']) && $post['sign'] === 'update') { // var_dump($post);exit;
                # 修改，加队员，分任务...
                // var_dump(arPOST('data'));exit;
                $new = arPOST('data');
                $db = U_item_taskModel::model() -> getDb();
                $comissionOldSql = 'select * from u_item_comission where i_id='.$_SESSION['i_id'].' and keep=1';
                $comissionOld = $db -> sqlQuery($comissionOldSql);

                foreach ($new as $keyNew => $valueNew) {
                    if (isset($valueNew['sign'])) {
                        # 删除一个职位...
                        $data = 0; // 返回数据
                        # u_item_comission 表中修改
                        $condition = array('i_id' => $_SESSION['i_id'], 'type' => $valueNew['id'], 'keep' => 1);
                        $flag = U_item_comissionModel::model() -> getDb() -> where($condition) -> update(array('keep' => 0));

                        # u_item_task 表中修改
                        if ($flag) {
                            // 记录到进度表 '项目概述','项目变更','成员变更','任务变更','提成变更'
                            $jobName = U_department_jobsModel::model() -> getDb() -> select('j_name') ->where('id='.$valueNew['id']) -> queryRow();
                            $content = '本项目删除【'.$jobName['j_name'].'】职务。';
                            $loginsert = array('i_id' => $_SESSION['i_id'], 'u_id' => $_SESSION['u_id'], 'type' => 5, 'Date' => date('Y-m-d H:i:s'), 'content' => $content);
                            $flagLog = U_item_develop_logModel::model() -> getDb() -> insert($loginsert);

                            if ($flagLog) {
                                # 查询出该职务参与项目的所有成员 u_item_task
                                $condition = array('i_id' => $_SESSION['i_id'], 'type' => $valueNew['id'], 'stay' => 1);
                                $members = U_item_taskModel::model() -> getDb() -> select('id, u_id') -> where($condition) -> queryAll();
                                # u_item_task 表中修改
                                foreach ($members as $k => $v) {
                                    $flagTask = U_item_taskModel::model() -> getDb() -> where('id='.$v['id']) -> update(array('stay' => 0));
                                    if($flagTask){
                                        // 记录到进度表 '项目概述','项目变更','成员变更','任务变更','提成变更'
                                        $nickname = U_usersModel::model() -> getDb() -> select('nickname') ->where('id='.$v['u_id']) -> queryRow();
                                        $content = '删除队员:'.$nickname['nickname'];
                                        $insertData = array('i_id' => $_SESSION['i_id'], 'u_id' => $_SESSION['u_id'], 'type' => 3, 'Date' => date('Y-m-d H:i:s'), 'content' => $content);
                                        $flagLogTask = U_item_develop_logModel::model() -> getDb() -> insert($insertData);
                                        if ($flagLogTask) {
                                            $data = 1;
                                        }

                                    }

                                }

                            }

                        }

                    }elseif (isset($valueNew['man'])) {
                        # 修改...
                        // 查询出参与项目的所有职位
                        $jobsIn = U_item_taskModel::model() -> getDb() -> select('type') -> where('i_id='.$_SESSION['i_id'].' and stay=1') -> group('type')-> order('type desc') -> queryAll();

                        if (!empty($jobsIn)) {
                            foreach ($jobsIn as $key => $value) {
                                $arr[] = $value['type'];
                            }
                        }else{
                            $arr = array();
                        }

                        $jobsIn = $arr; // var_dump($jobsIn);exit();
                        $man[] = $valueNew;

                        foreach ($man as $key => $value) {

                            $data = 0;
                            // 判断是新添加的职位还是原来的
                            if (in_array($value['id'], $jobsIn)) {
                                # 原来的
                                // 获取原始数据
                                $taskOldSql = 'select * from u_item_task where i_id='.$_SESSION['i_id'].' and stay=1 and type='.$value['id'].' order by id desc';
                                $taskOld = $db -> sqlQuery($taskOldSql);
                                // var_dump($taskOld);

                                // 数据处理
                                $taskNew = array();
                                $comissionNew = array();

                                // 对任务分离
                                foreach ($value['man'] as $k => $v) {
                                    $v['type'] = $value['id'];
                                    $v['u_id'] =$v['id'];
                                    unset($v['id']);
                                    $taskNew[] = $v;
                                }

                                // 部门提成分离
                                $jobName = U_department_jobsModel::model() -> getDb() -> select('j_name') ->where('id='.$value['id']) -> queryRow();
                                $comissionNew[$key]['jobName'] = $jobName['j_name'];
                                $comissionNew[$key]['type'] = $value['id'];
                                $comissionNew[$key]['percent'] = $value['percent'];

                                // 分析数据 task
                                $keepTask = array();
                                $addTask = array();
                                $delTask = array();
                                $taskOldVs = array();

                                foreach ($taskNew as $key => $newValue) {
                                    // 保留数据
                                    foreach ($taskOld as $k => $oldValue) {
                                        if ($newValue['u_id'] === $oldValue['u_id'] && $newValue['type'] === $oldValue['type']) {
                                            # 保留...
                                            $keepTask[] = $newValue;
                                            break;
                                        }

                                    }

                                }

                                // delTask
                                foreach ($keepTask as $k => $value) {

                                    foreach ($taskOld as $key => $oldValue) {

                                        if ($oldValue['u_id'] === $value['u_id'] && $oldValue['type'] === $value['type']) {
                                            // array_splice($taskOld, $key, 1);
                                            $taskOldVs[] = $taskOld[$key];
                                            unset($taskOld[$key]);
                                            break;

                                        }

                                    }

                                }

                                // addTask
                                foreach ($taskNew as $key => $newValue) {

                                    foreach ($keepTask as $k => $value) {

                                        if ($newValue['u_id'] === $value['u_id'] && $newValue['type'] === $value['type']) {
                                            // array_splice($taskNew, $key, 1);
                                            unset($taskNew[$key]);
                                            break;

                                        }

                                    }

                                }

                                // delTask
                                $delTask = $taskOld;
                                // addTask
                                $addTask = $taskNew;

                                // 分析数据 comission
                                $keepComission = array();
                                $comissionOldVs = array();

                                // 保留数据 keepComission
                                foreach ($comissionNew as $key => $newValue) {
                                    // 保留数据
                                    foreach ($comissionOld as $k => $oldValue) {
                                        if ($newValue['type'] === $oldValue['type']) {
                                            # 保留...
                                            $keepComission[] = $newValue;
                                            $comissionOldVs[] = $oldValue;
                                            break;
                                        }

                                    }

                                }

                                // 将数据写入数据库
                                # 1. task
                                # 1.1 add 写入 u_item_task 表并记录
                                foreach ($addTask as $key => $value) {
                                    # 记录到task表...
                                    if (isset($value['percent']) && $value['percent'] && isset($value['duty']) && $value['duty']) {

                                        $insertData = array('u_id' => $value['u_id'], 'i_id' => $_SESSION['i_id'], 'type' => $value['type'], 'percent' => $value['percent'], 'duty' => $value['duty'], 'stay' => 1);

                                    }elseif (isset($value['percent']) && $value['percent'] && !isset($value['duty'])) {

                                        $insertData = array('u_id' => $value['u_id'], 'i_id' => $_SESSION['i_id'], 'type' => $value['type'], 'percent' => $value['percent'], 'stay' => 1);

                                    }elseif (!isset($value['percent']) && isset($value['duty']) && $value['duty']) {

                                        $insertData = array('u_id' => $value['u_id'], 'i_id' => $_SESSION['i_id'], 'type' => $value['type'], 'duty' => $value['duty'], 'stay' => 1);

                                    }elseif (!isset($value['percent']) && !isset($value['duty'])) {

                                        $insertData = array('u_id' => $value['u_id'], 'i_id' => $_SESSION['i_id'], 'type' => $value['type'], 'stay' => 1);

                                    }

                                    $flag = U_item_taskModel::model() -> getDb() -> insert($insertData);
                                    # 记录到进度表
                                    if ($flag) {
                                        # code...  '项目概述','项目变更','成员变更','任务变更','提成变更'
                                        // $nickname = U_usersModel::model() -> getDb() -> select('nickname') ->where('id='.$value['u_id']) -> queryRow();
                                        if (isset($value['duty']) && $value['duty']) {
                                            $str = ',并分配任务：'.$value['duty'];
                                        }else{
                                            $str = '';
                                        }
                                        // $content = '添加队员:'.$nickname['nickname'].$str;
                                        $content = '添加队员:'.$value['nickname'].$str;
                                        $data = array('i_id' => $_SESSION['i_id'], 'u_id' => $_SESSION['u_id'], 'type' => 3, 'Date' => date('Y-m-d H:i:s'), 'content' => $content);
                                        U_item_develop_logModel::model() -> getDb() -> insert($data);

                                    }

                                }

                                # 1.1 add 添加的队员写入 u_item 表
                                $users = $this -> itemUserAll($_SESSION['i_id']);
                                U_itemsModel::model() -> getDb() -> where('id='.$_SESSION['i_id']) -> update(array('users' => $users));

                                # 1.2 del 在 u_item_task 表中修改并记录
                                foreach ($delTask as $key => $value) {
                                    # 到task表修改 ...
                                    $flag = U_item_taskModel::model() -> getDb() -> where('id='.$value['id']) -> update(array('stay' => 0));
                                    # 记录到进度表
                                    if ($flag) {
                                        # code...  '项目概述','项目变更','成员变更','任务变更','提成变更'
                                        $nickname = U_usersModel::model() -> getDb() -> select('nickname') ->where('id='.$value['u_id']) -> queryRow();
                                        $content = '删除队员:'.$nickname['nickname'];
                                        $data = array('i_id' => $_SESSION['i_id'], 'u_id' => $_SESSION['u_id'], 'type' => 3, 'Date' => date('Y-m-d H:i:s'), 'content' => $content);
                                        U_item_develop_logModel::model() -> getDb() -> insert($data);

                                    }

                                }

                                # 1.2 del 删除的队员 u_item 表修改
                                $users = $this -> itemUserAll($_SESSION['i_id']);
                                U_itemsModel::model() -> getDb() -> where('id='.$_SESSION['i_id']) -> update(array('users' => $users));

                                # 1.3 keep 保留数据
                                // var_dump($keepTask);
                                foreach ($keepTask as $key => $value) {
                                    $str = '';
                                    foreach ($value as $k => $v) {
                                        if (isset($taskOldVs[$key][$k]) && $value[$k] != $taskOldVs[$key][$k]) {
                                            switch ($k) {
                                                case 'duty':
                                                    $keyC = '具体任务';
                                                    break;
                                                case 'percent':
                                                    $keyC = '提成比例';
                                                    break;

                                                default:
                                                    $keyC = '其他';
                                                    break;
                                            }

                                            $str .= '【'.$value['u_id'].'-'.$value['type'].'】"'.$keyC.'"发生变化,由'.$taskOldVs[$key][$k].'变成'.$value[$k].'。\n';

                                        }

                                    }

                                    if ($str) {
                                        //记录到进度表
                                        // '项目概述','项目变更','成员变更','任务变更','提成变更'
                                        $loginsert = array('content' => $str, 'type' => 4, 'u_id' => $_SESSION['u_id'], 'i_id' => $_SESSION['i_id'], 'Date' => date('Y-m-d H:i:s'));
                                        // 向数据库写入进度
                                        U_item_develop_logModel::model() -> getDb() -> insert($loginsert);

                                    }

                                }

                                # 2. comission
                                foreach ($keepComission as $key => $value) {
                                    $str = '';
                                    foreach ($value as $k => $v) {
                                        if (isset($comissionOldVs[$key][$k]) && $value[$k] != $comissionOldVs[$key][$k]) {
                                            switch ($k) {
                                                case 'type':
                                                    $keyC = '职务';
                                                    break;
                                                case 'percent':
                                                    $keyC = '提成比例';
                                                    break;

                                                default:
                                                    $keyC = '其他';
                                                    break;
                                            }

                                            // comission 修改
                                            $flagComissionEdit = U_item_comissionModel::model() -> getDb() -> where('id='.$comissionOldVs[$key]['id']) -> update(array('percent' => $value['percent']));

                                            if ($flagComissionEdit) {

                                                $str .= '【'.$value['jobName'].'】'.$keyC.'发生变化,由'.$comissionOldVs[$key][$k].'变成'.$value[$k].'。\n';

                                            }

                                        }

                                    }

                                    if ($str) {
                                        // 记录到进度表
                                        // '项目概述','项目变更','成员变更','任务变更','提成变更'
                                        $loginsert = array('content' => $str, 'type' => 5, 'u_id' => $_SESSION['u_id'], 'i_id' => $_SESSION['i_id'], 'Date' => date('Y-m-d H:i:s'));
                                        // 向数据库写入进度
                                        U_item_develop_logModel::model() -> getDb() -> insert($loginsert);

                                    }

                                }

                            }else{
                                # 添加的
                                // var_dump($value);exit;
                                $add[] = $value;
                                foreach ($add as $key => $value1) {
                                    // 数据处理
                                    $taskNew = array();
                                    $comissionNew = array();

                                    // 对任务分离
                                    foreach ($value1['man'] as $k => $v) {
                                        $v['type'] = $value1['id'];
                                        $v['u_id'] =$v['id'];
                                        unset($v['id']);
                                        $taskNew[] = $v;
                                    }

                                    // 部门提成分离
                                    $jobName = U_department_jobsModel::model() -> getDb() -> select('j_name') ->where('id='.$value1['id']) -> queryRow();
                                    $comissionNew[$key]['jobName'] = $jobName['j_name'];
                                    $comissionNew[$key]['type'] = $value1['id'];
                                    $comissionNew[$key]['percent'] = $value1['percent'];

                                    // task
                                    foreach ($taskNew as $key => $value) {
                                        # 记录到task表...
                                        if (isset($value['percent']) && $value['percent'] && isset($value['duty']) && $value['duty']) {

                                            $insertData = array('u_id' => $value['u_id'], 'i_id' => $_SESSION['i_id'], 'type' => $value['type'], 'percent' => $value['percent'], 'duty' => $value['duty'], 'stay' => 1);

                                        }elseif (isset($value['percent']) && $value['percent'] && !isset($value['duty'])) {

                                            $insertData = array('u_id' => $value['u_id'], 'i_id' => $_SESSION['i_id'], 'type' => $value['type'], 'percent' => $value['percent'], 'stay' => 1);

                                        }elseif (!isset($value['percent']) && isset($value['duty']) && $value['duty']) {

                                            $insertData = array('u_id' => $value['u_id'], 'i_id' => $_SESSION['i_id'], 'type' => $value['type'], 'duty' => $value['duty'], 'stay' => 1);

                                        }elseif (!isset($value['percent']) && !isset($value['duty'])) {

                                            $insertData = array('u_id' => $value['u_id'], 'i_id' => $_SESSION['i_id'], 'type' => $value['type'], 'stay' => 1);

                                        }

                                        $flag = U_item_taskModel::model() -> getDb() -> insert($insertData);
                                        # 记录到进度表
                                        if ($flag) {
                                            # code...  '项目概述','项目变更','成员变更','任务变更','提成变更'
                                            if (isset($value['duty']) && $value['duty']) {
                                                $str = ',并分配任务：'.$value['duty'];
                                            }else{
                                                $str = '';
                                            }

                                            $content = '添加队员:'.$value['nickname'].$str;
                                            $data = array('i_id' => $_SESSION['i_id'], 'u_id' => $_SESSION['u_id'], 'type' => 3, 'Date' => date('Y-m-d H:i:s'), 'content' => $content);
                                            U_item_develop_logModel::model() -> getDb() -> insert($data);

                                        }

                                    }

                                    # 添加的队员写入 u_item 表
                                    $users = $this -> itemUserAll($_SESSION['i_id']);
                                    U_itemsModel::model() -> getDb() -> where('id='.$_SESSION['i_id']) -> update(array('users' => $users));

                                    // comission
                                    foreach ($comissionNew as $key => $value) {
                                        # 记录到 u_item_comissiom 表...
                                        $insertData = array('i_id' => $_SESSION['i_id'], 'type' => $value['type'], 'percent' => $value['percent'], 'keep' => 1);
                                        $flag = U_item_comissionModel::model() -> getDb() -> insert($insertData);
                                        # 记录到进度表
                                        if ($flag) {
                                            # code...  '项目概述','项目变更','成员变更','任务变更','提成变更'
                                            // 获得项目的名称
                                            $itemName = U_itemsModel::model() -> getDb() -> select('i_name') -> where('id='.$_SESSION['i_id']) -> queryRow();
                                            $content = $itemName['i_name'].'项目添加职位:'.$value['jobName'].',提成比例是：'.$value['percent'].'%';
                                            $data = array('i_id' => $_SESSION['i_id'], 'u_id' => $_SESSION['u_id'], 'type' => 1, 'Date' => date('Y-m-d H:i:s'), 'content' => $content);
                                            U_item_develop_logModel::model() -> getDb() -> insert($data);

                                        }

                                    }

                                }

                            }

                            $data = 1;
                        }

                    }elseif (count($valueNew) == 1 && isset($valueNew['id']) ) {
                        # 只添加职位并无提成和人员的加入...
                        $insertData = array('i_id' => $_SESSION['i_id'], 'type' => $valueNew['id'], 'keep' => 1 );
                        if (U_item_comissionModel::model() -> getDb() -> insert($insertData)) {
                            # 记录到进度表...
                            // '项目概述','项目变更','成员变更', '任务变更', '提成变更'
                            $type =  1;
                            // 获得项目的名称
                            $itemName = U_itemsModel::model() -> getDb() -> select('i_name') -> where('id='.$_SESSION['i_id']) -> queryRow();
                            $jobName = U_department_jobsModel::model() -> getDb() -> select('j_name') -> where('id='.$valueNew['id']) -> queryRow();
                            $content = $itemName['i_name'].'项目添加职位:'.$jobName['j_name'].'。';
                            $loginsert = array('content' => $content, 'type' => $type, 'u_id' => $_SESSION['u_id'], 'i_id' => $_SESSION['i_id'], 'Date' => date('Y-m-d H:i:s'));
                            U_item_develop_logModel::model() -> getDb() -> insert($loginsert);
                            $data = 1; // success
                        }else{
                            $data = 0; // false
                        }

                    }else{
                        # comission
                        // 职务提成修改
                        $comissionNew[] = $valueNew;
                        $keepComission = array();
                        $comissionOldVs = array();

                        // 分析数据 comission
                        foreach ($comissionNew as $key => &$newValue) {
                            $jobName = U_department_jobsModel::model() -> getDb() -> select('j_name') ->where('id='.$newValue['id']) -> queryRow();
                            $newValue['jobName'] = $jobName['j_name'];
                            $newValue['type'] = $newValue['id'];
                            unset($newValue['id']);
                            // 保留数据
                            foreach ($comissionOld as $k => $oldValue) {
                                if ($newValue['type'] === $oldValue['type']) {
                                    # 保留...
                                    $keepComission[] = $newValue;
                                    $comissionOldVs[] = $comissionOld[$k];
                                    break;
                                }

                            }

                        }

                        foreach ($keepComission as $key => $value) {

                            $data = 0; // 返回数据
                            $str = '';
                            foreach ($value as $k => $v) {

                                if (isset($comissionOldVs[$key][$k]) && $value[$k] != $comissionOldVs[$key][$k]) {

                                    switch ($k) {
                                        case 'type':
                                            $keyC = '职务';
                                            break;
                                        case 'percent':
                                            $keyC = '提成比例';
                                            break;

                                        default:
                                            $keyC = '其他';
                                            break;
                                    }

                                    $str .= '【'.$value['jobName'].'】'.$keyC.'发生变化,由'.$comissionOldVs[$key][$k].'变成'.$value[$k].'。\n';

                                }

                            }

                            // 数据库中修改 u_item_comissiom
                            $flag = U_item_comissionModel::model() -> getDb() -> where('id='.$comissionOldVs[$key]['id']) -> update(array('percent' => $value['percent']));

                            // 记录到进度表
                            if ($flag) {
                                // '项目概述','项目变更','成员变更','任务变更','提成变更'
                                $loginsert = array('content' => $str, 'type' => 5, 'u_id' => $_SESSION['u_id'], 'i_id' => $_SESSION['i_id'], 'Date' => date('Y-m-d H:i:s'));
                                // 向数据库写入进度
                                U_item_develop_logModel::model() -> getDb() -> insert($loginsert);

                            }

                            $data = 1;

                        }

                    }

                }

                echo json_encode($data);
                exit;

            }elseif (isset($post['sign']) && $post['sign'] === 'more') {
                // 最新情况
                $newInf = U_item_develop_logModel::model() -> getDb() -> select('u_id, content, Date') -> where('i_id='.$_SESSION['i_id']) -> order('id desc') -> limit('4') -> queryAll();
                foreach ($newInf as $key => &$value) {
                    // 操作人员的名字
                    $name = U_usersModel::model() -> getDb() -> select('nickname') -> where('id='.$value['u_id']) -> queryRow();
                    $value['nickname'] = $name['nickname'];
                    // 时间的处理
                    $time = strtotime($value['Date']);
                    $timeD = time() - $time;
                    $days = floor($timeD/3600/24);
                    $hours = floor(($timeD%(3600*24))/3600);
                    $mins = floor(($timeD%3600)/60);
                    if ($days) {
                        $value['time'] = $days.'天前';
                    }
                    if (!$days && $hours) {
                        $value['time'] = $hours.'小时前';
                    }
                    if (!$days && !$hours && $mins) {
                        $value['time'] = $mins.'分前';
                    }
                    if (!$days && !$hours && !$mins){
                        $value['time'] = 0..'分前';
                    }

                    unset($value['u_id']);
                    unset($value['Date']);
                }
                echo json_encode($newInf);
                exit;
            }elseif (isset($post['sign']) && $post['sign'] === 'applyJob') {
                # 项目申请
                // 是否登陆
                if (!isset($_SESSION['u_id'])) {
                    $data = 0; // 要跳转到首页去登陆

                }else{
                    // 一次申请一个
                    $insertData = array('i_id' => $_SESSION['i_id'], 'u_id' => $_SESSION['u_id'], 'type' => $post['data'], 'flag' => 0);
                    // 申请提交
                    $applyFlag = U_item_task_applyModel::model() -> getDb() -> insert($insertData);
                    if ($applyFlag) {
                        # 记录到进度表...
                        // '项目概述','项目变更','成员变更', '任务变更', '提成变更'
                        $type =  3;
                        // 获得项目的名称
                        $itemName = U_itemsModel::model() -> getDb() -> select('i_name') -> where('id='.$_SESSION['i_id']) -> queryRow();
                        $jobName = U_department_jobsModel::model() -> getDb() -> select('j_name') -> where('id='.$post['data']) -> queryRow();
                        $content = '用户：'.$_SESSION['nickname'].'申请加入'.$itemName['i_name'].'项目。参与'.$jobName['j_name'];
                        $loginsert = array('content' => $content, 'type' => $type, 'u_id' => $_SESSION['u_id'], 'i_id' => $_SESSION['i_id'], 'Date' => date('Y-m-d H:i:s'));

                        $log = U_item_develop_logModel::model() -> getDb() -> insert($loginsert);
                        $data = 1; // 提交成功
                    }else{
                        $data = 2; // 提交失败
                    }
                }
                echo json_encode($data);
                exit;
            }elseif (isset($post['sign']) && $post['sign'] === 'xiugaishenqing') {
                # 申请的处理...['sign':'xxx', 'data':{'act':'agree','id':'xxx'}]
                // 获取原信息
                $applyOld = U_item_task_applyModel::model() -> getDb() -> where('id='.$post['data']['id']) -> queryRow();
                // 获得项目的名称
                $itemName = U_itemsModel::model() -> getDb() -> select('i_name') -> where('id='.$applyOld['i_id']) -> queryRow();
                // 职位名称
                $jobName = U_department_jobsModel::model() -> getDb() -> select('j_name') -> where('id='.$applyOld['u_id']) -> queryRow();
                // 哪位用户
                $userName = U_usersModel::model() -> getDb() -> select('nickname') -> where('id='.$applyOld['u_id']) -> queryRow();
                if ($post['data']['act'] == 'agree') {
                    # 审核中的同意...
                    $updateData = array('flag' => 1);
                    $content = '用户：'.$userName['nickname'].'成功加入'.$itemName['i_name'].'项目。参与'.$jobName['j_name'];
                    // 在任务表中添加一条记录
                    $insertData = array('i_id' => $applyOld['i_id'],'u_id' => $applyOld['u_id'],'type' => $applyOld['type'],'stay' =>1);
                    U_item_taskModel::model() -> getDb() -> insert($insertData);
                }else{
                    # 审核中的不同意
                    $updateData = array('flag' => 0);
                    $content = '用户：'.$userName['nickname'].'加入'.$itemName['i_name'].'项目，参与'.$jobName['j_name'].'申请失败!';
                }
                $applyFlag = U_item_task_applyModel::model() -> getDb() -> where('id='.$post['data']['id']) -> update($updateData);
                if ($applyFlag) {
                    # 记录到进度表...
                    // '项目概述','项目变更','成员变更', '任务变更', '提成变更'
                    $type =  3;
                    $loginsert = array('content' => $content, 'type' => $type, 'u_id' => $_SESSION['u_id'], 'i_id' => $_SESSION['i_id'], 'Date' => date('Y-m-d H:i:s'));
                    $log = U_item_develop_logModel::model() -> getDb() -> insert($loginsert);
                    $data = 1; // success
                }else{
                    $data = 0; // false
                }
                echo json_encode($data);
                exit;
            }


        }

        if (arGET()) {
            // var_dump($_SESSION);exit;
            // var_dump(arGET());exit;
            // 将项目的 id 保存在 session 中，在项目管理中要用 arComp('list.session') -> set('u_id', $flage);
            arComp('list.session') -> set('i_id', arGET('id'));


            // 查询具体的某一项目
            $itemInf = U_itemsModel::model() -> getDb() -> where('id = '.$_SESSION['i_id']) -> queryRow();
            // 开发周期的处理
            if (preg_match('/d/', $itemInf['days'])) {
                $itemInf['days'] = preg_replace('/d/', '天', $itemInf['days']);

            }elseif (preg_match('/w/', $itemInf['days'])) {
                $itemInf['days'] = preg_replace('/w/', '周', $itemInf['days']);

            }elseif (preg_match('/m/', $itemInf['days'])) {
                $itemInf['days'] = preg_replace('/m/', '月', $itemInf['days']);

            }elseif (preg_match('/y/', $itemInf['days'])) {
                $itemInf['days'] = preg_replace('/y/', '年', $itemInf['days']);

            }

            // 合同日期和发布日期处理
            $itemInf['contractDate'] = date('Y-m-d', strtotime($itemInf['contractDate']));
            $itemInf['releaseDate'] = date('Y-m-d', strtotime($itemInf['releaseDate']));


            // 查询谋项目状态
            $itemStatus = U_item_status_typeModel::model() -> getDb() -> select('name') -> where('id = '.$itemInf['status']) -> queryRow();
            $itemInf['statusN'] = $itemStatus['name'];

            // 查询谋项目发布人
            $publisher = U_usersModel::model() -> getDb() -> select('nickname') -> where(array('id' => $itemInf['publisher'])) -> queryRow();
            if ($publisher) :
                $itemInf['publisher'] = $publisher['nickname'];
            else :
                $itemInf['publisher'] = 'coopcoder平台';
            endif;

            // 项目需求描述
            $itemInf['requirement'] = preg_replace('/(<img.+?src=")/', "\${1}".arComp('url.route')->host()."/Upload/", $itemInf['requirement']);
            // 项目的状态信息(多)
            // $itemStatus = $this -> itemStatus(); // 需要分配不？？
            $this -> assign (array('itemInf' => $itemInf ));
        }


        // 数据测试 task
        // 1. 所有 task 数据 降序排列(职位id 再是 task 的 id )
        // $taskSql = 'select t.*, u.nickname, j.j_name  from u_item_task as t, u_users as u, u_department_jobs as j where u.id=t.u_id and t.type=j.id and i_id=19 and t.stay=1 order by t.type desc,t.id desc';
        // 2. 获取所有的部门 降序排列(职位id)
        // $jobsSql = 'select t.type, j.j_name  from u_item_task as t, u_department_jobs as j where t.type=j.id and i_id=19 and t.stay=1 group by t.type order by t.type desc';
        // $db = U_item_taskModel::model() -> getDb();
        // $task = $db -> sqlQuery($taskSql);
        // $jobs = $db -> sqlQuery($jobsSql);
        // $this -> assign(array('task' => $task, 'jobs' => $jobs));


        // task 的职位 倒序
        // $db = U_item_taskModel::model() -> getDb();
        // $taskJobsSql = 'select t.type as j_id, j_name from u_item_task as t, u_department_jobs as j where t.type=j.id and i_id='.$_SESSION['i_id'].' and t.stay=1 group by t.type order by t.type desc';
        // $taskJobsSql = 'select t.type as j_id, j_name from u_item_task as t, u_department_jobs as j where t.type=j.id and i_id=18 and t.stay=1 group by t.type order by t.type desc';
        // $taskJobs = $db -> sqlQuery($taskJobsSql);
        // if ($taskJobs) {
        //     echo 'YOU';
        //     exit;
        // }else{
        //     echo 'wu';
        //     exit;
        // }

        // 该项目的所有队员及任务 倒序
        // $memberSql = 'select t.id as t_id, t.type as j_id, t.u_id, duty, t.percent, u.nickname, j.j_name  from u_item_task as t, u_users as u, u_department_jobs as j where u.id=t.u_id and t.type=j.id and i_id='.$_SESSION['i_id'].' and t.stay=1 order by t.type desc,t.id desc';
        // $member = $db -> sqlQuery($memberSql);

        // 数据准备
        $data = array();
        $db = U_item_taskModel::model() -> getDb();
        // 通过task表查询
        // $taskJobsSql = 'select t.type as id, j_name from u_item_task as t, u_department_jobs as j where t.type=j.id and i_id='.$_SESSION['i_id'].' and t.stay=1 group by t.type order by t.type desc';
        // $taskJobsSql = 'select t.type as id, j_name from u_item_task as t, u_department_jobs as j where t.type=j.id and i_id=18 and t.stay=1 group by t.type order by t.type desc';
        // $taskJobsSql = 'SELECT type as id FROM u_item_task WHERE i_id='.$_SESSION['i_id'].' and stay=1 GROUP BY type ORDER BY type DESC';
        // $taskJobs = $db -> sqlQuery($taskJobsSql);
        // foreach ($taskJobs as $key => &$value) {
        //     // 职务名称
        //     $jobName = U_department_jobsModel::model() -> getDb() -> select('j_name') -> where('id='.$value['id']) -> queryRow();
        //     $value['j_name'] = $jobName['j_name'];
        //     // 职务所占比例
        //     $jobPerSql = 'select percent from u_item_comission where i_id ='.$_SESSION['i_id'].' and keep=1 and type ='.$value['id'];
        //     // $jobPerSql = 'select percent from u_item_comission where i_id ='.$_SESSION['i_id'].' and type =1';
        //     $jobPer = $db -> sqlQuery($jobPerSql);
        //     if (!empty($jobPer)) {
        //         $value['percent'] = $jobPer[0]['percent'];
        //     }else{
        //         $value['percent'] = '';
        //     }

        //     // 队员
        //     $memberSql = 'select t.u_id as id, duty, t.percent, u.nickname, u.tel, u.qq, u.email, u.weixin  from u_item_task as t, u_users as u where u.id=t.u_id and t.type ='.$value['id'].' and i_id='.$_SESSION['i_id'].' and t.stay=1 order by t.type desc,t.id desc';
        //     $member = $db -> sqlQuery($memberSql);
        //     $value['man'] = $member;

        //     // $data[$value['j_name']] = $value;

        //     // 登陆者与本项目的关系
        //     if (isset($_SESSION['u_id'])) {
        //         // 1. 在任务表中查询是否参与
        //         $taskArr = array('i_id' => $_SESSION['i_id'], 'u_id' => $_SESSION['u_id'], 'type' => $value['id'], 'stay' => 1 );
        //         $taskRelation = U_item_taskModel::model() -> getDb() -> where($taskArr) -> queryRow();
        //         if ($taskRelation) {
        //             $value['relation'] = '参与';
        //         }else{
        //             # 在申请表中查询
        //             $applyArr = array('i_id' => $_SESSION['i_id'], 'u_id' => $_SESSION['u_id'], 'type' => $value['id'] );
        //             $applyRelation = U_item_task_applyModel::model() -> getDb() -> select('flag') -> where($applyArr ) -> queryRow();
        //             if ($applyRelation) {
        //                 if ($applyRelation['flag'] == 0) {
        //                     $value['relation'] = '审核中';
        //                 }elseif ($applyRelation['flag'] == 1) {
        //                    $value['relation'] = '参与';
        //                 }elseif ($applyRelation['flag'] == 2) {
        //                     $value['relation'] = '申请失败';
        //                 }
        //             }else{
        //                 $value['relation'] = '申请';
        //             }
        //         }
        //     }else{
        //         $value['relation'] = 'unlisted';
        //     }

        //     $data[] = $value; // 人员任务分配情况


        // }

        // 通过comission表查询
        $taskJobsSql = 'SELECT type as id, percent FROM u_item_comission WHERE i_id='.$_SESSION['i_id'].' and keep=1 ORDER BY type DESC';
        $taskJobs = $db -> sqlQuery($taskJobsSql);
        foreach ($taskJobs as $key => &$value) {
            // 职务名称
            $jobName = U_department_jobsModel::model() -> getDb() -> select('j_name') -> where('id='.$value['id']) -> queryRow();
            $value['j_name'] = $jobName['j_name'];

            // 队员
            $memberSql = 'select t.u_id as id, duty, t.percent, u.nickname, u.tel, u.qq, u.email, u.weixin  from u_item_task as t, u_users as u where u.id=t.u_id and t.type ='.$value['id'].' and i_id='.$_SESSION['i_id'].' and t.stay=1 order by t.type desc,t.id desc';
            $member = $db -> sqlQuery($memberSql);
            $value['man'] = $member;

            // $data[$value['j_name']] = $value;

            // 登陆者与本项目的关系
            if (isset($_SESSION['u_id'])) {
                // 1. 在任务表中查询是否参与
                $taskArr = array('i_id' => $_SESSION['i_id'], 'u_id' => $_SESSION['u_id'], 'type' => $value['id'], 'stay' => 1 );
                $taskRelation = U_item_taskModel::model() -> getDb() -> where($taskArr) -> queryRow();
                if ($taskRelation) {
                    $value['relation'] = '参与';
                }else{
                    # 在申请表中查询
                    $applyArr = array('i_id' => $_SESSION['i_id'], 'u_id' => $_SESSION['u_id'], 'type' => $value['id'] );
                    $applyRelation = U_item_task_applyModel::model() -> getDb() -> select('flag') -> where($applyArr ) -> queryRow();
                    if ($applyRelation) {
                        if ($applyRelation['flag'] == 0) {
                            $value['relation'] = '审核中';
                        }elseif ($applyRelation['flag'] == 1) {
                           $value['relation'] = '参与';
                        }elseif ($applyRelation['flag'] == 2) {
                            $value['relation'] = '申请失败';
                        }
                    }else{
                        $value['relation'] = '申请';
                    }
                }
            }else{
                $value['relation'] = 'unlisted';
            }

            $data[] = $value; // 人员任务分配情况


        }
        // $data = $taskJobs;
        // echo json_encode($data);
        // exit;

        // 最新的动态
        $newInf = U_item_develop_logModel::model() -> getDb() -> select('u_id, content, Date') -> where('i_id='.$_SESSION['i_id']) -> order('id desc') -> limit('4') -> queryAll();
        foreach ($newInf as $key => &$value) {
            // 操作人员的名字
            $name = U_usersModel::model() -> getDb() -> select('nickname') -> where('id='.$value['u_id']) -> queryRow();
            $value['nickname'] = $name['nickname'];
            // 时间的处理
            $time = strtotime($value['Date']);
            $timeD = time() - $time;
            $days = floor($timeD/3600/24);
            $hours = floor(($timeD%(3600*24))/3600);
            $mins = floor(($timeD%3600)/60);
            if ($days) {
                $value['time'] = $days.'天前';
            }
            if (!$days && $hours) {
                $value['time'] = $hours.'小时前';
            }
            if (!$days && !$hours && $mins) {
                $value['time'] = $mins.'分前';
            }
            if (!$days && !$hours && !$mins){
                $value['time'] = '0分前';
            }

            unset($value['u_id']);
            unset($value['Date']);
        }
        // echo '<pre>';
        // var_dump($newInf);exit;

        // 本项目的申请情况(只显示申请的，若申请成功和失败的均不可在此处显示)
        $applyInSql = 'SELECT u.nickname, j.j_name, flag FROM u_users as u,u_item_task_apply AS a, u_department_jobs AS j WHERE a.u_id=u.id AND j.id=a.type AND flag=0 AND a.i_id='.$_SESSION['i_id'];
        $applyInf = U_item_task_applyModel::model() -> getDb() -> sqlQuery($applyInSql);

        // 本项目的申请情况
        // $applyInSql = 'SELECT u.nickname, j.j_name, flag FROM u_users as u,u_item_task_apply AS a, u_department_jobs AS j WHERE a.u_id=u.id AND j.id=a.type AND a.i_id='.$_SESSION['i_id'];
        // $applyInf = U_item_task_applyModel::model() -> getDb() -> sqlQuery($applyInSql);
        // foreach ($applyInf as $key => &$value) {
        //     if ($value['flag'] == 0) {
        //         $value['flag'] = '申请中';
        //     }elseif ($value['flag'] == 1) {
        //         $value['flag'] = '申请成功';
        //     }elseif ($value['flag'] == 2) {
        //         $value['flag'] = '申请失败';
        //     }
        // }
        // echo '<pre>';
        // var_dump($applyInf);exit;

        $this -> assign(array('data' => $data, 'newInf' => $newInf ));
        // if (isset($_SESSION['nickname'])) {
        //     $nickname = $_SESSION['nickname'];
        // }else{
        //     $nickname = '';
        // }
        // $this -> assign(array('nickname' => $nickname ));
        $this->display();
    }



    // 图片上传 用户资料修改
    public function post_fileAction(){
        if (arPOST()) {
            // 过滤字符
            $utils = new Utils();
            $post = $utils::shtmlspecialchars(arPOST());
            // var_dump($post);exit();
            // 还回数据
            $data = 0;
            // 获取原始数据
            $userInf = U_usersModel::model() -> getDb() -> select('nickname, photo, qq, email, weixin') -> where('id='.$_SESSION['u_id']) -> queryRow();
            // $userInf = U_usersModel::model() -> getDb() -> select('nickname, photo, qq, email, weixin') -> where('id=14') -> queryRow();
            // var_dump($userInf);//exit;
            $photo = $userInf['photo'];
            if (!isset($post['photo'])) {
                # 有上传图片
                // var_dump($_FILES);exit();
                $photo = arComp('ext.upload')->upload('photo', arComp('url.route')->pathToDir(AR_SERVER_PATH.'Upload'));
                // var_dump(arComp('ext.upload') -> errorMsg());

            }else{
               $photo = $userInf['photo'];

            }


            // 修改数据准备
            if ($post['nickname'] == '姓名')
                $post['nickname'] = $userInf['nickname'];

            if ($post['email'] == '邮箱')
                $post['email'] = $userInf['email'];

            if ($post['qq'] == 'QQ号')
                $post['qq'] = $userInf['qq'];

            $updateData = array('nickname' => $post['nickname'], 'email' => $post['email'], 'qq' => $post['qq'], 'photo' => $photo,);
            // var_dump($updateData);exit;
            $flag = U_usersModel::model() -> getDb() -> where('id='.$_SESSION['u_id']) -> update($updateData);
            if ($flag){
                $data = 1;
            }

            echo json_encode($data);
            exit;

        }

    }

    // 图片上传 项目详情编辑
    public function  itemPigAction(){
        // var_dump($_FILES);exit;
        if ($_FILES ){

            $pig = arComp('ext.upload')->upload('wangEditorH5File', arComp('url.route')->pathToDir(AR_SERVER_PATH.'Upload'));
            // var_dump(arComp('ext.upload') -> errorMsg()); // arComp('url.route')->pathToDir(AR_SERVER_PATH.'/Upload')
            $data = arComp('url.route')->host().'/Upload/'.$pig;
            echo $data; // 图片 url
            exit;

        }
    }


    // // 图片上传 详情编辑
    // public function  itemPigAction(){
    //     // var_dump($_FILES);exit;
    //     if ($_FILES ){

    //         $pig = arComp('ext.upload')->upload('wangEditorH5File', arComp('url.route')->pathToDir(AR_SERVER_PATH.'/Upload'));
    //         // var_dump(arComp('ext.upload') -> errorMsg()); // arComp('url.route')->pathToDir(AR_SERVER_PATH.'/Upload')
    //         $data = arComp('url.route')->host().'/Upload/'.$pig;
    //         echo $data;
    //         exit;

    //     }

    // }

    // 退出系统
    public function logoutAction(){
        //清除session，并跳转到登录页面
        arModule('Lib.User')->clearSession();
        $this->redirect(array('index', array('action' => 'logout')));

    }

    public function testAction(){
        // if ($this -> delUser(26)) {
        //     echo 1;
        // }else{
        //     echo 0;
        // }
        // exit;
        // erCode
        // include 'main/Ext/QRcode.class.php';
        // $value = 'http://www.baidu.com'; //二维码内容
        // $errorCorrectionLevel = 'L';//容错级别
        // $matrixPointSize = 250/29;//生成图片大小
        //生成二维码图片
        // QRcode::png($value, 'Upload/code.png', $errorCorrectionLevel, $matrixPointSize, 2);

        // var_dump($this -> allDepJob());
        // 项目结算 i_id 参与某个项目的人员结算
        // 项目的金额
        $iMoney = U_itemsModel::model() -> getDb()-> select('money') -> where('id='.$_SESSION['i_id']) -> queryRow();
        // 参与的人员
        $members = U_item_taskModel::model() -> getDb() -> select('id, u_id, type, percent, duty, completion, status') -> where('i_id='.$_SESSION['i_id'].' and stay=1') ->queryAll();
        foreach ($members as $key => &$value) {
            // 哪位用户
            $nickname = U_usersModel::model() -> getDb() -> select('nickname') -> where('id='.$value['u_id']) -> queryRow();
            $value['nickname'] = $nickname['nickname'];
            // 什么职位
            $jobName = U_department_jobsModel::model() -> getDb() -> select('j_name') -> where('id='.$value['type']) -> queryRow();
            $value['j_name'] = $jobName['j_name'];
            // 职位所在比例
            $comission = U_item_comissionModel::model() -> getDb() -> select('percent') -> where('i_id='.$_SESSION['i_id'].' and keep=1 and type='.$value['type']) -> queryRow();
            $value['moneyAll'] = $iMoney['money'];
            $value['jobPercent'] = $comission['percent'];
            $value['money'] =  $value['moneyAll'] * $value['jobPercent'] * $value['percent'] /10000;
            // $value['money'] = $iMoney['money'] * $comission['percent'] * $value['percent'] * $value['completion'] /1000000;
            unset($value['u_id']);
            unset($value['type']);
        }
        var_dump($members);
        exit;
    }

    public function test1Action(){
        // |<img /> 标签中图片地址的配置
        // $str1 = '<img src="http://www.baidu.com/quickoa/Upload/1.jpg" >';

        // $str = preg_replace('/http:\/\/.+?\//', "/", $str1); // 域名下
        // $str = preg_replace('/http:\/\/.+\//', "", $str1); // 只有图片地址
        // $ste2 = "&lt;?php echo arComp('url.route')->host().'/Upload/';?&gt:";
        // $str1 = '<img src="1.jpg" >';
        // $str = preg_replace('/<img.+src="/', arComp('url.route')->host()."/Upload/", $str1);
        // $str = '<img src="'.arComp('url.route')->host().'/Upload/1.jpg" >';

        // echo $str;exit;
        // echo arComp('url.route')->host().'/Upload/';
        // $this -> assign(array('str' => $str));
        $post['data'] = 'page0';
        $num = preg_replace('/page/', '', $post['data']);
        $total = U_item_task_applyModel::model() -> getDb() -> count();
        $min = 0;
        $max = ceil($total/5);
        $num = preg_replace('/page/', '', $post['data']); // 获取是第几页
        if ($num <= $min) {
            // $limit = 0; // 分页的起始位置
            $allApply = '-1';
        }elseif ($num > $max) {
            // $limit = ($max -1 ) * 5; // 分页的起始位置
            $allApply = 'large';
        }else{
            $limit = ($num -1 ) * 5; // 分页的起始位置
            $allApplySql = 'SELECT a.*, u.nickname, i.i_name, j.j_name FROM u_item_task_apply AS a, u_users AS u, u_items as i, u_department_jobs as j  WHERE j.id=a.type AND u.id=a.u_id AND i.id=a.i_id ORDER BY a.i_id DESC limit '.$limit.',5';
            $allApply = U_item_task_applyModel::model() -> getDb() -> sqlQuery($allApplySql);
            foreach ($allApply as $key => &$value) {
                if ($value['flag'] == 0) {
                    $value['flag'] = '申请';
                }elseif ($value['flag'] == 1) {
                    $value['flag'] = '成功';
                }elseif ($value['flag'] == 0) {
                    $value['flag'] = '拒绝';
                }
            }
        }
        // $limit = ($num -1 ) * 5; // 分页的起始位置

        echo json_encode($allApply);
        $this -> display();
    }

    // 添加用户的方法（含选择部门和职位） $data 是要添加用户的数据
    public function addUser($data){
        if (U_usersModel::model() -> getDb() -> insert($data)) {
            return true; // success
        }else{
            return false; // false
        }
    }

    // 修改某个用户的方法（含选择部门和职位）$data 是要修改的数据, $id 用户的id
    public function editUserAction($data, $id){
        if (U_usersModel::model() -> getDb() -> where('id='.$id) -> queryRow()) {
            if (U_usersModel::model() -> getDb() -> where('id='.$id) -> update($data)) {
                return true; // success
            }else{
                return false; // false
            }
        }else{
            return false; // 不存在该用户
        }
    }

    // 删除用户的方法（含选择部门和职位）$str 是用户的id
    public function delUser($str){
        if (U_usersModel::model() -> getDb() -> where('id='.$str) -> queryRow()) {
            if (U_usersModel::model() -> getDb() -> where('id='.$str) -> delete()) {
                return true; // success
            }else{
                return false; // false
            }
        }else{
            return false; // 不存在该用户
        }

    }

    // 项目管理 -> 项目成员管理
    public function pm2Action()
    {
        $this->assign(array('cssInsertBundles' => array('user')));
        $this->assign(array('jsInsertBundles' => array('user')));
        $this->display();
    }

    // 项目成员管理 -> 人员审核
    public function pm3Action()
    {
        $this->assign(array('cssInsertBundles' => array('user')));
        $this->assign(array('jsInsertBundles' => array('user')));
        $this->display();
    }


}