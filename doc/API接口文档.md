# API接口文档

**协议版本：v0.1.0**

**Protocol Version：v0.1.0**

[TOC]

## 介绍

通过每课的 API Server，你可以获得学生、老师、教室等数据以进行校园相关应用开发。



### API Key

在使用前，你需要先申请 API Key。若无特别说明，以下接口均需要 API Key。API Key 的使用方法为在 HTTP Header 中加入 `X-API-KEY`，内容为你的 API Key。



### 更新日志

**特别提醒**：所有线上服务，协议版本均应保持版本号前两位为一致的。

**更新原则**：若只发生文档更新，前两位版本号将不发生变化。若接口内容发生变化，协议版本号前两位将会被更新并写入更新日志。

#### v0.1.0

初始版本，未发布前版本



## 模糊搜索

### 学生教师搜索

* URL：`/search/{搜索内容}`

* 方法：`GET`

* 参数（Query string）：
  * `page_num`：数字，分页页数（从0计数）
  * `page_size`：数字，分页大小（最小值为2，最大值为100）

* 成功响应：

  * `status`：响应结果，正常情况下为`success`
  * `data`：搜索结果，读取方式请参考示例
  * `info`：辅助信息，包括`page_num`、`page_size`、`count`
    * `page_num`：分页页数
    * `page_size`：分页大小
    * `count`：data 长度

* 说明：

  * 支持使用简拼、全拼、中文全字进行搜索，例如搜索“每课”，可通过"mk"、"meike"、“每课”进行搜索，暂不支持其他搜索方案。
  * 若姓名中出现符号，可以忽略符号进行拼音搜索。例如搜索“每·课”，可通过"mk"、"meike"、“每·课”进行搜索，暂不支持其他搜索方案。
  * 外籍学生若使用中文名称可使用拼音搜索，若使用英文名称，请使用完整的姓名进行搜索。
  * Foreign students can use Pinyin search if they use Chinese names. If they use English names, please use the full name to search.
  * 搜索关键词小于两个字符将按照异常请求处理。

* 请求示例：

  ```
  GET /search/fhx?page_size=5&page_num=1
  ```

* 响应示例：

  ```json
  {
      "status": "success",
      "data": [
          {
              "student_id": "0201130230",
              "name": "范海辛",
              "type": "student",
              "semesters": [
                  "2016-2017-1",
                  "2016-2017-2"
              ],
              "deputy": "文学院",
              "class": "城地1602"
          },
          {
              "student_id": "0204130270",
              "name": "返魂香",
              "type": "student",
              "semesters": [
                  "2016-2017-1",
                  "2016-2017-2"
              ],
              "deputy": "资源与安全工程学院",
              "class": "城地1302"
          }
      ],
      "info": {
          "page_num": 1,
          "page_size": 5,
          "count": 2
      }
  }
  ```

  

## 信息查询

### 课程查询

* URL：`/course/{学期}/{课程编号}`
* 方法：`GET`
* 说明：
  * 学期格式形如：`2018-2019-1`

* 请求示例：

  ```
  GET /course/2018-2019-1/0D8EAEC14F3E4EE38C039C6072218FA7
  ```

* 响应示例：

  ```json
  {
      "status": "success",
      "name": "Web应用开发技术",
      "course_id": "0D8EAEC14F3E4EE38C039C6072218FA7",
      "type": "专业选修课",
      "weeks": [11,12,13,14,15,16,17,18],
      "week_string": "11-18/全周",
      "lesson": "10506",
      "union_name": "软件1701-03",
      "room": "世B502",
      "room_id": "2430502",
      "hour": 32,
      "picked": 95,
      "students": [
          {
              "name": "毕水秀",
              "student_id": "1909170222",
              "class": "软件1703",
              "deputy": "软件学院"
          },
          {
              "name": "周福",
              "student_id": "0304170106",
              "class": "软件1701",
              "deputy": "软件学院"
          },
          ...
      ],
      "teachers": [
          {
              "name": "外聘1",
              "teacher_id": "0000187",
              "title": "教授",
              "unit": "软件学院"
          }
      ]
  }
  ```

### 教室查询

- URL：`/room/{教室编号}/{学期}`

- 方法：`GET`

- 说明：

  - 学期格式形如：`2018-2019-1`

- 请求示例：

  ```
  GET /room/2430402/2018-2019-1
  ```

- 响应示例：

  ```json
  {
      "status": "success",
      "name": "世B402",
      "room_id": "2430402",
      "building": "世B",
      "campus": "铁道校区",
      "course": [
          {
              "name": "毛泽东思想与中国特色社会主义理论体系概论",
              "course_id": "F3AA2FE5715C4CDFAAB1DDE56B500097",
              "room": "世B402",
              "room_id": "2430402",
              "weeks": [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18],
              "week_string": "1-18/全周",
              "lesson": "10506",
              "teachers": [
                  {
                      "teacher_id": "119043",
                      "name": "胡厚荣",
                      "title": "高级政工师"
                  }
              ]
          },
          {
              "name": "毛泽东思想与中国特色社会主义理论体系概论",
              "course_id": "AABF91ADE89244179D3587BB1CC2DF0E",
              "room": "世B402",
              "room_id": "2430402",
              "weeks": [13,14,15,16,17,18],
              "week_string": "13-18/全周",
              "lesson": "40506",
              "teacher": [
                  {
                      "teacher_id": "119043",
                      "name": "胡厚荣",
                      "title": "高级政工师"
                  }
              ]
          },
          ...
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
      "student_id": "3901160407",
      "deputy": "计算机学院",
      "class": "软件1604",
      "semesters": [
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

- 请求示例：

  ```
  GET /student/3901160407/timetable/2018-2019-1
  ```

- 响应示例：

  ```json
  {
      "status": "success",
      "name": "詹泽宇", 
      "student_id": "3901160407",
      "deputy": "计算机学院",
      "class": "软件1604",
      "semesters": [
          "2018-2019-1",
          "2016-2017-1",
          "2016-2017-2",
          "2017-2018-1",
          "2017-2018-2",
          "2018-2019-2"
      ],
      "courses": [
          {
              "name": "日语（二外）",
              "course_id": "10B1D23F9CFA4FC6BD885904C07FA7AB",
              "room": "世B102",
              "room_id": "2430102",
              "weeks": [3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18],
              "week_string": "3-18/全周",
              "lesson": "10102",
              "teacher": [
                  {
                      "teacher_id": "702134",
                      "name": "金涛",
                      "title": "讲师（高校）"
                  }
              ]
          },
          {
              "name": "云计算及应用",
              "course_id": "23AA42B2C02544828961859CB0E2F1E2",
              "room": "世B402",
              "room_id": "2430402",
              "weeks": [11,12,13,14,15,16,17,18],
              "week_string": "11-18/全周",
              "lesson": "30102",
              "teacher": [
                  {
                      "teacher_id": "212178",
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
      "title": "副教授",
      "unit": "软件学院",
      "semesters": [
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

- 请求示例：

  ```
  GET /teacher/212178/timetable/2018-2019-1
  ```

- 响应示例：

  ```json
  {
      "status": "success",
      "name": "邓磊",
      "title": "副教授",
      "unit": "软件学院",
      "semesters": [
          "2018-2019-1",
          "2016-2017-1",
          "2016-2017-2",
          "2017-2018-1",
          "2017-2018-2",
          "2018-2019-2"
      ],
      "courses": [
          {
              "name": "大型数据库技术",
              "course_id": "12E4C3DCB631491DB7F56F13873349C1",
              "room": "世B402",
              "room_id": "2430402",
              "weeks": [3,4,5,6,7,8,9,10],
              "week_string": "3-10/全周",
              "lesson": "10102",
              "teachers": [
                  {
                      "teacher_id": "邓磊",
                      "name": "212178",
                      "title": "副教授"
                  }
              ]
          },
          {
              "name": "云计算及应用",
              "course_id": "42654979C8F540BA9956AFF401E73F5B",
              "room": "世B402",
              "room_id": "2430402",
              "weeks": [11,12,13,14,15,16,17,18],
              "week_string": "11-18/全周",
              "lesson": "10102",
              "teachers": [
                  {
                      "teacher_id": "邓磊",
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

### 服务端信息获取

- URL：`{host}/info`

- 方法：`GET`

- 请求示例：

  ```
  GET /info
  ```

- 响应示例：

  ```json
  {
      "status": "success",
      "info": "线上版本：0.0.1",
      "version": "0.0.1",
      "data_update_time": "2019-3-1"
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

