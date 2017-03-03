<?php

/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/12/27
 * Time: 10:08
 */
class MemberModel extends ArModel
{
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_users';


    // 展示用户
    public function memberList($condition = array())
    {
        // 对数据进行分页
        $condition[] = array('id >' => 2);
        $totalCount = MemberModel::model()->getDb()
            ->where($condition)
            ->count();

        $page = new Page($totalCount, 10);

        $rows = MemberModel::model()->getDb()
            ->limit($page->limit())
            ->where($condition)
            ->order('id asc')
            ->queryAll();

        // 数据预处理
        $rows = $this->preEditMember($rows);

        $pageHtml = $page->show();

        return array('rows' => $rows, 'pageHtml' => $pageHtml, 'totalCount' => $totalCount);

    }


    // 预处理用户格式
    public function preEditMember(array $rows)
    {
        // 数据预处理
        foreach ($rows as $index => $value) {
            $rows[$index]['registerDate'] = date('Y-m-d', strtotime($value['registerDate']));

            // 处理头像
            if ($rows[$index]['photo']) {
                $rows[$index]['photo'] = arCfg('UPLOAD_FILE_SERVER_PATH') . $value['photo'];
            } else {
                $rows[$index]['photo'] = arCfg('DEFAULT_USER_LOG');
            }

            // 用户所在部门
            $rows[$index]['department'] = $this->editData($rows[$index]['department'], 'U_departmentModel', 'd_name');

            // 用户的职位
            $rows[$index]['job'] = $this->editData($rows[$index]['job'], 'U_department_jobsModel', 'j_name');
        }

        return $rows;

    }


    // 对部门，职位等处理
    public function editData($value, $model, $field)
    {
        if ($value != "") {
            if (strpos(trim($value, ','), ",")) {
                $rows = explode(',', $value);
                // 循环前先将数组清空
                $row = array();
                foreach ($rows as $v) {
                    $row[] = $this->joinData($model, 'id', $v, $field);
                }
                // 去除数组中空值和重复值
                $row = array_unique(array_filter($row));

                if (count($row) == 0) {
                    $values = "";
                } else {
                    $values = implode(',', $row);
                }
                return $values;
            } else {
                $values = $this->joinData($model, 'id', $value, $field);
                return $values;
            }
        }

    }


    // 关联查询数据
    public function joinData($model, $condition, $where, $select)
    {
        $joinData = $model::model()->getDb()
            ->where(array("$condition" => $where))
            ->queryColumn("$select");

        return $joinData;

    }


    // 删除用户
    public function deleteMember($id)
    {
        $row = MemberModel::model()->getDb()
            ->where(array('id' => $id))
            ->delete();

        if ($row) {
            return $row;
        }

    }


    // 添加用户
    public function addMember($param)
    {
        // 预处理参数
        if (isset($param['departments'])) {
            $departments = $param['departments'];
            $departments = array_unique(array_filter($departments));
            $departments = implode(',', $departments);
        } else {
            $departments = "";
        }

        if (isset($param['departmentJobs'])) {
            $departmentJobs = $param['departmentJobs'];
            $departmentJobs = array_unique(array_filter($departmentJobs));
            $departmentJobs = implode(',', $departmentJobs);
        } else {
            $departmentJobs = "";
        }

        $password = md5(substr(md5(md5($param['password']) . 'listen'), 6, 6));

        // 对添加项目时成员为空进行处理
        $add = MemberModel::model()->getDb()
            ->insert(array(
                "nickname" => $param['nickname'],
                "password" => $password,
                "tel" => $param['tel'],
                "email" => $param['email'],
                "qq" => $param['qq'],
                "weixin" => $param['weixin'],
                "registerDate" => $param['registerDate'],
                "salary" => $param['salary'],
                "level" => $param['level'],
                "department" => $departments,
                "job" => $departmentJobs,
            ));

        // 取出当前生成id
        $id = MemberModel::model()->getDb()
            ->where(array('tel' => $param['tel']))
            ->queryColumn('id');

        // 计算用户编号
        $usernum = 'MZM' . str_pad($id, 5, "0", STR_PAD_LEFT);

        // 保存用户编号
        $usernum = MemberModel::model()->getDb()
            ->where(array('id' => $id))
            ->update(array('user_number' => $usernum));

        if ($add) {
            return true;
        }
    }


    // 编辑用户
    public function updateMember($param)
    {
        // 预处理参数
        if (isset($param['departments'])) {
            $departments = $param['departments'];
            $departments = array_unique(array_filter($departments));
            $departments = implode(',', $departments);
        } else {
            $departments = "";
        }

        if (isset($param['departmentJobs'])) {
            $departmentJobs = $param['departmentJobs'];
            $departmentJobs = array_unique(array_filter($departmentJobs));
            $departmentJobs = implode(',', $departmentJobs);
        } else {
            $departmentJobs = "";
        }

        $add = MemberModel::model()->getDb()
            ->where(array('id' => $param['id']))
            ->update(array(
                "nickname" => $param['nickname'],
                "tel" => $param['tel'],
                "email" => $param['email'],
                "qq" => $param['qq'],
                "weixin" => $param['weixin'],
                "registerDate" => $param['registerDate'],
                "salary" => $param['salary'],
                "level" => $param['level'],
                "department" => $departments,
                "job" => $departmentJobs,
            ));

        if (isset($add)) {
            return true;
        }
    }


    // 添加用户时查询数据
    public function checkMember()
    {
        // 查询出所有部门
        $departments = U_departmentModel::model()->getDb()
            ->select('d_name,id')
            ->queryAll();

        // 查询出所有的职务
        $departmentJobs = U_department_jobsModel::model()->getDb()
            ->select('j_name,id')
            ->queryAll();

        return array('departments' => $departments, 'departmentJobs' => $departmentJobs);

    }


    // 回显项目数据
    public function preShowList($id)
    {
        $list = MemberModel::model()->getDb()
            ->where(array('id' => $id))
            ->queryRow();

        if ($list['user_number'] == "") {
            // 计算用户编号
            $list['user_number'] = 'MZM' . str_pad($id, 5, "0", STR_PAD_LEFT);

            // 保存用户编号
            MemberModel::model()->getDb()
                ->where(array('id' => $id))
                ->update(array('user_number' => $list['user_number']));
        }

        // 回显部门 职务
        $list['department'] = array_unique(explode(',', trim($list['department'], ",")));
        $list['job'] = array_unique(explode(',', trim($list['job'], ",")));

        return $list;

    }


    // 修改密码
    public function changePassword($param)
    {
        $password = md5(substr(md5(md5($param['password']) . 'listen'), 6, 6));

        $list = MemberModel::model()->getDb()
            ->where(array('id' => $param['id']))
            ->update(array('password' => $password));

        if (isset($list)) {
            return true;
        }

    }

}