# Everyclass-Entity

**协议版本：v0.1.0**

**Protocol Version：v0.1.0**

[TOC]

## 介绍

通过每课的 API Server，你可以获得学生、老师、教室等数据以进行校园相关应用开发。



### 更新日志

**特别提醒**：所有线上服务，协议版本均应保持版本号前两位为一致的。

**更新原则**：若只发生文档更新，前两位版本号将不发生变化。若接口内容发生变化，协议版本号前两位将会被更新并写入更新日志。

#### v0.1.0

初始版本，未发布前版本

#### v0.2.0

* 因课程相关用词体系修改，原有`course_list`修改为`card_list`。
* `course_list`中原有的`course_code`修改为`card_code`。
* `course_list`中原有的`class`修改为`tea_class`表示一个行政班。
* 每个card中添加`course_code`字段，表示课程号（如：140102X1）。

#### v0.2.1

* 学生信息中新增新增`campus`字段，表示学生所在校区。
* 教师信息中新增新增`degree`字段，表示教师学历（可能是空字符串）。
* 更新学生信息获取方式，优先获取当前学期的信息。

## 基础接口

### 测试连通性

- 说明：基本连通性测试接口

- 请求示例：

  ```
  GET /
  ```

- 响应示例：

  ```json
  {
    "code": 92000,
    "data": {
      "info": "Hello, world!",
      "status": "success"
    },
    "method": "hello_world",
    "status": "OK",
    "time": 1589279052,
    "timestamp": "2020-05-12 18:24:13"
  }
  ```

### 服务信息

- 说明：主要提供服务状态、数据版本、接口版本等信息

- 请求示例：

  ```
  GET /info/service
  ```

- 响应示例：

  ```json
  {
    "code": 92000,
    "data": {
      "data_time": "2020-04-26",
      "service_notice": "服务正常运行",
      "service_state": "running",
      "status": "success",
      "version": "0.3.0"
    },
    "method": "service_info",
    "status": "OK",
    "time": 1589279872,
    "timestamp": "2020-05-12 18:37:52"
  }
  ```




## 搜索接口

### 对象搜索

* 说明：提供首页搜索功能服务接口

* 请求示例：

  ```
  GET /search/query?key=fhx&group=teacher
  ```

* 参数（Query string）：

  * `key`：字符串，搜索值（搜索字符串最短不可小于2个字符）
  * `group`：字符串，搜索分类（可在`course`,`teacher`,`student`,`room`中选择一项）

* 说明：

  * 支持使用简拼、全拼、中文全字进行搜索，例如搜索“每课”，可通过"mk"、"meike"、“每课”进行搜索，暂不支持其他搜索方案。
  * 若姓名中出现符号，可以忽略符号进行拼音搜索。例如搜索“每·课”，可通过"mk"、"meike"、“每·课”进行搜索，暂不支持其他搜索方案。
  * 外籍学生若使用中文名称可使用拼音搜索，若使用英文名称，请使用完整的姓名进行搜索。
  * Foreign students can use Pinyin search if they use Chinese names. If they use English names, please use the full name to search.

* 响应示例：

  ```json
  {
      "status": "success",
      "data": [
          {
              "code": "0201130230",
              "name": "返魂香",
              "group": "teacher",
              "title": "副教授",
              "unit": "软件学院",
              "semester_list": [
                  "2018-2019-1",
                  "2016-2017-1",
                  "2016-2017-2",
                  "2017-2018-1",
                  "2017-2018-2",
                  "2018-2019-2"
              ]
          },
          {
              "code": "0201130230",
              "name": "范海辛",
              "group": "student",
              "deputy": "文学院",
              "class": "城地1602",
              "semester_list": [
                  "2016-2017-1",
                  "2016-2017-2"
              ]
          }
  	]
  }
  ```



### 空教室搜索

* 请求示例：

  ```
  GET /search/room/available?week=15&session=10102&campus=新校区&building=A座
  ```

* 参数（Query string）：

  * `week`：字符串，周次（必填）
  * `session`：字符串，节次（必填）
  * `campus`：字符串，校区（可选，参照`/room`返回值）
  * `building`：字符串，建筑（可选，参照`/room`返回值）

* 说明：

  * 填充参数`building`时必须填充`campus`参数

* 响应示例：

  ```json
  {
      "room_list": [
          {
              "code": "9010102",
              "info": {
                  "course_code": "140101X10",
                  "course_name": "大学物理A（一）",
                  "lesson": "B19251E6198F44519FD2B0B0C5BD7AC7"
              },
              "name": "A座102"
          },
          {
              "code": "9010110",
              "info": {
                  "course_code": "090141X20",
                  "course_name": "数字信号处理",
                  "lesson": "C0C4782A1A0746C893A19DDBE38E57C4"
              },
              "name": "A座110"
          },
          {
              "code": "9010122",
              "info": {
                  "course_code": "080211X10",
                  "course_name": "机械设计",
                  "lesson": "127A77A68741411986FE6B09D7EF8BAB"
              },
              "name": "A座122"
          },
          ......
      ],
      "status": "OK"
  }
  ```

  

## 信息查询

### 卡片查询

* URL：`/card/{课程编号}/timetable/{学期}`
* 方法：`GET`
* 说明：
  * 学期格式形如：`2018-2019-1`
  * 响应中不包含该课程的其他学期，semester字段仅表示响应数据所属的学期。

* 请求示例：

  ```
  GET /card/0D8EAEC14F3E4EE38C039C6072218FA7/timetable/2018-2019-1
  ```

* 响应示例：

  ```json
  {
      "status": "success",
      "name": "Web应用开发技术",
      "room": "世B502",
      "hour": 32,
      "type": "专业选修课",
      "picked": 95,
      "lesson": "10506",
      "tea_class": "软件1701-03",
      "card_code": "0D8EAEC14F3E4EE38C039C6072218FA7",
      "room_code": "2430502",
      "week_list": [11,12,13,14,15,16,17,18],
      "course_code": "140102X1",
      "week_string": "11-18/全周",
      "semester": "2018-2019-1",
      "student_list": [
          {
              "name": "毕水秀",
              "student_code": "1909170222",
              "class": "软件1703",
              "deputy": "软件学院"
          },
          {
              "name": "周福",
              "student_code": "0304170106",
              "class": "软件1701",
              "deputy": "软件学院"
          }
      ],
      "teacher_list": [
          {
              "name": "外聘1",
              "teacher_code": "0000187",
              "title": "教授",
              "unit": "软件学院"
          }
      ]
  }
  ```



### 教室查询

#### 教室位置分组

- 说明：提供校区、教学楼、教室之间的层级关系

- 请求示例：

  ```
  GET /room
  ```

- 响应示例：

  ```json
  {
      "room_group": {
          "南校区": {
              "一教": [
                  "1310419",
                  "1310420"
              ],
              "三教": [
                  "351104",
                  "351203",
                  "351301",
                  "351302",
                  "351303",
                  "351304",
                  "351305"
              ],
              "二教": [
                  "1320213",
                  "1320216",
                  "324204",
                  "324207"
              ]
          }
          ......
      },
      "status": "OK"
  }
  ```

  

#### 教室课表信息

- URL：`/room/{教室编号}/timetable/{学期}`

- 方法：`GET`

- 说明：

  - 学期格式形如：`2018-2019-1`
  - 响应中不包含该课程的其他学期，semester字段仅表示响应数据所属的学期。

- 请求示例：

  ```
  GET /room/2430402/timetable/2018-2019-1
  ```

- 响应示例：

  ```json
  {
      "status": "success",
      "name": "世B402",
      "campus": "铁道校区",
      "building": "世B",
      "room_code": "2430402",
      "semester": "2018-2019-1",
      "semester_list": [
          "2018-2019-1",
          "2016-2017-1",
          "2016-2017-2",
          "2017-2018-1",
          "2017-2018-2",
          "2018-2019-2"
      ]
      "card_list": [
          {
              "name": "毛泽东思想与中国特色社会主义理论体系概论",
              "room": "世B402",
              "lesson": "10506",
              "card_code": "F3AA2FE5715C4CDFAAB1DDE56B500097",
              "room_code": "2430402",
              "week_list": [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18],
              "week_string": "1-18/全周",
              "course_code": "140102X1",
              "teacher_list": [
                  {
                      "teacher_code": "119043",
                      "name": "胡厚荣",
                      "title": "高级政工师"
                  }
              ]
          },
          {
              "name": "毛泽东思想与中国特色社会主义理论体系概论",
              "room": "世B402",
              "lesson": "10506",
              "card_code": "F3AA2FE5715C4CDFAAB1DDE56B500097",
              "room_code": "2430402",
              "week_list": [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18],
              "week_string": "1-18/全周",
              "course_code": "140102X1",
              "teacher_list": [
                  {
                      "teacher_code": "119043",
                      "name": "胡厚荣",
                      "title": "高级政工师"
                  }
              ]
          }
      ]
  }
  ```

### 学生查询

#### 查询学生基本信息

- URL：`{host}/student/{学生编号}`
- 方法：`GET`
- 请求示例：

  ```
  GET /student/3901160407
  ```

- 响应示例：

  ```json
  {
      "status": "success",
      "name": "詹泽宇", 
      "student_code": "3901160407",
      "deputy": "计算机学院",
      "class": "软件1604",
      "campus": "铁道校区",
      "semester_list": [
          "2018-2019-1",
          "2016-2017-1",
          "2016-2017-2",
          "2017-2018-1",
          "2017-2018-2",
          "2018-2019-2"
      ]
  }
  ```

#### 查询学生课表

- URL：`{host}/student/{学生编号}/timetable/{学期}`

- 方法：`GET`

- 说明：

  - 学期格式形如：`2018-2019-1`
  - 学生编号格式：编号包含数字与字母
  - semester字段：仅表示响应数据所属的学期。

- 请求示例：

  ```
  GET /student/3901160407/timetable/2018-2019-1
  ```

- 响应示例：

  ```json
  {
      "status": "success",
      "name": "詹泽宇", 
      "student_code": "3901160407",
      "deputy": "计算机学院",
      "class": "软件1604",
      "campus": "铁道校区",
      "semester": "2018-2019-1",
      "semester_list": [
          "2018-2019-1",
          "2016-2017-1",
          "2016-2017-2",
          "2017-2018-1",
          "2017-2018-2",
          "2018-2019-2"
      ],
      "card_list": [
          {
              "name": "日语（二外）",
              "room": "世B102",
              "lesson": "10102",
              "card_code": "10B1D23F9CFA4FC6BD885904C07FA7AB",
              "room_code": "2430102",
              "week_list": [3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18],
              "course_code": "390121Z10",
              "week_string": "3-18/全周",
              "teacher_list": [
                  {
                      "teacher_code": "702134",
                      "name": "金涛",
                      "title": "讲师（高校）"
                  }
              ]
          },
          {
              "name": "云计算及应用",
              "room": "世B402",
              "lesson": "30102",
              "card_code": "23AA42B2C02544828961859CB0E2F1E2",
              "room_code": "2430402",
              "week_list": [11,12,13,14,15,16,17,18],
              "course_code": "390121Z10",
              "week_string": "11-18/全周",
              "teacher_list": [
                  {
                      "teacher_code": "212178",
                      "name": "邓磊",
                      "title": "副教授"
                  }
              ]
          },
          ...
      ]
  }
  ```



### 老师查询

#### 查询教师基本信息

- URL：`{host}/teacher/{教师编号}`

- 方法：`GET`

- 说明：
  
- 学期格式形如：`2018-2019-1`
  
- 请求示例：

    ```
    GET /teacher/212178
    ```

- 响应示例：

  ```json
  {
      "status": "success",
      "name": "邓磊",
      "unit": "软件学院",
      "title": "副教授",
      "degree": "博士毕业",
      "teacher_code": "131352",
      "semester_list": [
          "2018-2019-1",
          "2016-2017-1",
          "2016-2017-2",
          "2017-2018-1",
          "2017-2018-2",
          "2018-2019-2"
      ]
  }
  ```

#### 查询教师课表

- URL：`{host}/teacher/{教师编号}/timetable/{学期}`

- 方法：`GET`

- 说明：

  - 学期格式形如：`2018-2019-1`
  - 教师编号格式：编号包含数字与字母
  - semester字段：仅表示响应数据所属的学期。

- 请求示例：

  ```
  GET /teacher/212178/timetable/2018-2019-1
  ```

- 响应示例：

  ```json
  {
      "status": "success",
      "name": "邓磊",
      "unit": "软件学院",
      "title": "副教授",
      "degree": "博士毕业",
      "semester": "2018-2019-1",
      "teacher_code": "131352",
      "semester_list": [
          "2018-2019-1",
          "2016-2017-1",
          "2016-2017-2",
          "2017-2018-1",
          "2017-2018-2",
          "2018-2019-2"
      ],
      "card_list": [
          {
              "name": "大型数据库技术",
              "room": "世B402",
              "lesson": "10102",
              "card_code": "12E4C3DCB631491DB7F56F13873349C1",
              "room_code": "2430402",
              "week_list": [3,4,5,6,7,8,9,10],
              "course_code": "390121Z10",
              "week_string": "3-10/全周",
              "teacher_list": [
                  {
                      "teacher_code": "邓磊",
                      "name": "212178",
                      "title": "副教授"
                  }
              ]
          },
          {
              "name": "云计算及应用",
              "room": "世B402",
              "lesson": "10102",
              "card_code": "42654979C8F540BA9956AFF401E73F5B",
              "room_code": "2430402",
              "week_list": [11,12,13,14,15,16,17,18],
              "course_code": "390121Z10",
              "week_string": "11-18/全周",
              "teacher_list": [
                  {
                      "teacher_code": "邓磊",
                      "name": "212178",
                      "title": "副教授"
                  }
              ]
          },
          ...
      ]
  }
  ```




## 数据字典

在数据交换的过程中出现的以下键值，可以按照以下解释理解含义。

| 名称   | 含义                     | 类型 |
| ------ | ------------------------ | ---- |
| klass  | ~~非法字段，请反馈~~     |      |
| course | 两个连续的课时           |      |
| class  | 学生所属班级（非行政班） |      |
|        |                          |      |
|        |                          |      |
|        |                          |      |
|        |                          |      |
|        |                          |      |
|        |                          |      |
|        |                          |      |
|        |                          |      |
|        |                          |      |
|        |                          |      |
|        |                          |      |
|        |                          |      |
|        |                          |      |
|        |                          |      |
|        |                          |      |
|        |                          |      |

