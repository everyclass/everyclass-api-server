# API接口文档

**协议版本：v0.1.0**

**Protocol Version：v0.1.0**

[TOC]

## 介绍

通过每课的 API Server，你可以获得学生、老师、教室等数据以进行校园相关应用开发。



### API Key

在使用前，你需要先申请 API Key。若无特别说明，以下接口均需要 API Key。API Key 的使用方法为在 HTTP Header 中加入 `X-Auth-Token`，内容为你的 API Key。



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

## 模糊搜索

### 综合搜索

* URL：`/search/query?{搜索参数}`

* 方法：`GET`

* 参数（Query string）：

  * `key`：字符串，搜索值（搜索字符串最短不可小于2个字符）
  * `type`：字符串数组、搜索分类（可在`student`,`teacher`,`room`,`vague_room`中选择一项或多项）
  * `page_size`：数字，分页大小（默认值20，最小值2，最大值100）
  * `page_index`：数字，分页页数（默认值为1，从1计数的分页下标）
  * `sort_key`：字符串，排序主键（可在`code`,`name`,`type`中选择**一项**）
  * `sort_order`：字符串，排序方式（默认为`AES`，可在`ASC`,`DESC`中选择**一项**）

* 成功响应：

  * `status`：响应结果，正常情况下为`success`
  * `data`：搜索结果，读取方式请参考示例
  * `info`：辅助信息，包括`page_num`、`page_size`、`count`
    * `page_size`：分页大小（用户指定的分页大小）
    * `page_index`：分页页数（从1计数的分页下标）
    * `page_num`：分页数量（按照当前分页大小计算）
    * `count`：data 数组长度（请根据type类型区分不同的对象）

* 说明：

  * 支持使用简拼、全拼、中文全字进行搜索，例如搜索“每课”，可通过"mk"、"meike"、“每课”进行搜索，暂不支持其他搜索方案。
  * 若姓名中出现符号，可以忽略符号进行拼音搜索。例如搜索“每·课”，可通过"mk"、"meike"、“每·课”进行搜索，暂不支持其他搜索方案。
  * 外籍学生若使用中文名称可使用拼音搜索，若使用英文名称，请使用完整的姓名进行搜索。
  * Foreign students can use Pinyin search if they use Chinese names. If they use English names, please use the full name to search.
  * 搜索关键词小于两个字符将按照异常请求处理。
  * 若未设置分类参数则查询所有分类的数据，**但不可将分类设置为空**。
  * 当排序主键不存在时，排序方式将不会生效。

* 请求示例：

  ```
  GET /search/query?key=fhx&type[]=teacher&type[]=student&page_size=5&page_index=1&sort_key=type&sort_order=DESC
  ```

* 响应示例：

  ```json
  {
      "status": "success",
      "data": [
          {
              "teacher_code": "0201130230",
              "name": "返魂香",
              "type": "teacher",
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
              "student_code": "0201130230",
              "name": "范海辛",
              "type": "student",
              "deputy": "文学院",
              "class": "城地1602",
              "semester_list": [
                  "2016-2017-1",
                  "2016-2017-2"
              ]
          }
  	],
      "info": {
          "page_index": 1,
          "page_size": 3,
          "page_num": 2,
          "count": 3
      }
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



## 元数据及其他接口

### 测试连通性

- URL：`/`

- 方法：`GET`

- 请求示例：

  ```
  GET /
  ```

- 响应示例：

  ```json
  {
      "status": "success",
      "info": "Hello, world!"
  }
  ```

### 服务信息

- URL：`{host}/info/service`

- 方法：`GET`

- 说明：

  - status：响应状态
  - version：接口版本
  - service_state：服务状态
  - service_notice：服务状态描述
  - data_time：数据更新时间

- 请求示例：

  ```
  GET /info/service
  ```

- 响应示例：

  ```json
  {
      "status": "success",
      "version": "0.1.0",
      "service_state": "running",
      "service_notice": "服务正常运行",
      "data_time": "2019-3-24"
  }
  ```

- 字段说明：
    - `status`：请求状态。`success`代表成功。
    - `version`：API Server 版本号
    - `data_update_time`：课表数据更新时间

### 健康检查

- URL：`{host}/info/health`

- 方法：`GET`

- 响应示例：

  ```json
  {
      "status": "success",
      "time": 1552675506,
      "MySQL": true,
      "MongoDB": true
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

