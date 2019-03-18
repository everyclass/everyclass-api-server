# API接口文档

**协议版本：v0.1.0**

**Protocol Version：v0.1.0**

[TOC]

## 综述



## 更新日志

**特别提醒**：所有线上服务，协议版本均应保持版本号前两位为一致的。

**更新原则**：若只发生文档更新，前两位版本号将不发生变化。若接口内容发生变化，协议版本号前两位将会被更新并写入更新日志。

### v0.1.0

初始版本，未发布前版本



## 模糊搜索

**提示**：当用户搜索数据时将自动记录搜索的请求数据

### 学生教师搜索

* URL：`{host}/search/query`

* 方式：`GET`

* 参数：

  * key：搜索的关键词（需要进行URL编码）
  * page_num：分页页数（从0计数）
  * page_size：分页大小（该参数最小值为2，最大值为100）

* 响应（成功时）：

  * data：该字段将储存搜索结果，读取方式请参考示例
  * info：该字段将记录辅助信息，包括`page_num`、`page_size`、`count`
    * page_num：分页页数
    * page_size：分页大小
    * count：data字段长度
  * status：success（响应结果）

* 说明：

  * 请求加密：自定义`X-Auth-Token`请求头，自定义`X-Auth-User`响应
  * 支持使用简拼、全拼、中文全字进行搜索，例如搜索“每课”，可通过"mk"、"meike"、“每课”进行搜索，暂不支持其他搜索方案。
  * 若姓名中出现符号，可以忽略符号进行拼音搜索。例如搜索“每·课”，可通过"mk"、"meike"、“每·课”进行搜索，暂不支持其他搜索方案。
  * 外籍学生若使用中文名称可使用拼音搜索，若使用英文名称，请使用完整的姓名进行搜索。
  * Foreign students can use Pinyin search if they use Chinese names. If they use English names, please use the full name to search.
  * 搜索关键词小于两个字符将按照异常请求处理。

* 请求示例：

  ```
  {host}/search/query?key=fhx&page_size=5&page_num=1
  ```

* 响应示例：

  ```json
  {
      "data": [
          {
              "code": "0201130230",
              "name": "范海辛",
              "type": "student",
              "semester": [
                  "2016-2017-1",
                  "2016-2017-2"
              ],
              "deputy": "文学院",
              "klass": "城地1602"
          },
          {
              "code": "0204130270",
              "name": "返魂香",
              "type": "student",
              "semester": [
                  "2016-2017-1",
                  "2016-2017-2"
              ],
              "deputy": "资源与安全工程学院",
              "klass": "城地1302"
          }
      ],
      "info": {
          "page_num": 1,
          "page_size": 5,
          "count": 2
      },
      "status": "success"
  }
  ```

  

## 课表查询

**提示**：当用户查询数据时将自动记录查询的请求数据

### 查询课程信息

* URL：`{host}/course/{学期}/{课程编号]`
* 方式：`GET`
* 说明：
  * 学期格式形如：`2018-2019-1`
  * 课程编号格式：编号包含数字与字母
  * 请求加密：自定义`X-Auth-Token`请求头，自定义`X-Auth-User`响应

* 请求示例：

  ```
  {host}/course/2018-2019-1/0D8EAEC14F3E4EE38C039C6072218FA7
  ```

* 响应示例：

  ```json
  {
      "status": "success",
      "name": "Web应用开发技术",
      "course_code": "0D8EAEC14F3E4EE38C039C6072218FA7",
      "room": "世B502",
      "room_code": "2430502",
      "week": [11,12,13,14,15,16,17,18],
      "week_str": "11-18/全周",
      "lesson": "10506",
      "klass": "软件1701-03",
      "pick": 95,
      "hour": 32,
      "type": "专业选修课",
      "student": [
          {
              "name": "毕水秀",
              "code": "1909170222",
              "class": "软件1703",
              "deputy": "软件学院"
          },
          {
              "name": "周福",
              "code": "0304170106",
              "class": "软件1701",
              "deputy": "软件学院"
          },
          ...
      ],
      "teacher": [
          {
              "name": "外聘1",
              "code": "0000187",
              "title": "教授",
              "unit": "软件学院"
          }
      ]
  }
  ```

### 查询教室信息

- URL：`{host}/room/{学期}/{教室编号}`

- 方式：`GET`

- 说明：

  - 学期格式形如：`2018-2019-1`
  - 课程编号格式：编号包含数字与字母
  - 请求加密：自定义`X-Auth-Token`请求头，自定义`X-Auth-User`响应

- 请求示例：

  ```
  {host}/room/2018-2019-1/2430402
  ```

- 响应示例：

  ```json
  {
      "status": "success",
      "code": "2430402",
      "name": "世B402",
      "building": "世Ｂ",
      "campus": "铁道校区",
      "semester": "2018-2019-1",
      "course": [
          {
              "name": "毛泽东思想与中国特色社会主义理论体系概论",
              "course_code": "F3AA2FE5715C4CDFAAB1DDE56B500097",
              "room": "世B402",
              "room_code": "2430402",
              "week": [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18],
              "week_str": "1-18/全周",
              "lesson": "10506",
              "teacher": [
                  {
                      "code": "119043",
                      "name": "胡厚荣",
                      "title": "高级政工师"
                  }
              ]
          },
          {
              "name": "毛泽东思想与中国特色社会主义理论体系概论",
              "course_code": "AABF91ADE89244179D3587BB1CC2DF0E",
              "room": "世B402",
              "room_code": "2430402",
              "week": [13,14,15,16,17,18],
              "week_str": "13-18/全周",
              "lesson": "40506",
              "teacher": [
                  {
                      "code": "119043",
                      "name": "胡厚荣",
                      "title": "高级政工师"
                  }
              ]
          },
          ...
      ]
  }
  ```

### 查询学生信息

- URL：`{host}/student/{学期}/{学生编号}`
- 方式：`GET`
- 说明：
  - 学期格式形如：`2018-2019-1`
  - 课程编号格式：编号包含数字与字母
  - 请求加密：自定义`X-Auth-Token`请求头，自定义`X-Auth-User`响应

- 请求示例：

  ```
  {host}/student/2018-2019-1/3901160407
  ```

- 响应示例：

  ```json
  {
      "status": "success",
      "code": "3901160407",
      "deputy": "计算机学院",
      "klass": "软件1604",
      "name": "詹泽宇",
      "semester": "2018-2019-1",
      "available_semester": [
          "2018-2019-1",
          "2016-2017-1",
          "2016-2017-2",
          "2017-2018-1",
          "2017-2018-2",
          "2018-2019-2"
      ],
      "course": [
          {
              "name": "日语（二外）",
              "course_code": "10B1D23F9CFA4FC6BD885904C07FA7AB",
              "room": "世B102",
              "room_code": "2430102",
              "week": [3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18],
              "week_str": "3-18/全周",
              "lesson": "10102",
              "teacher": [
                  {
                      "code": "702134",
                      "name": "金涛",
                      "title": "讲师（高校）"
                  }
              ]
          },
          {
              "name": "云计算及应用",
              "course_code": "23AA42B2C02544828961859CB0E2F1E2",
              "room": "世B402",
              "room_code": "2430402",
              "week": [11,12,13,14,15,16,17,18],
              "week_str": "11-18/全周",
              "lesson": "30102",
              "teacher": [
                  {
                      "code": "212178",
                      "name": "邓磊",
                      "title": "副教授"
                  }
              ]
          },
          ...
      ]
  }
  ```

### 查询教师信息

- URL：`{host}/teacher/{学期}/{教师编号}`

- 方式：`GET`

- 说明：
  - 学期格式形如：`2018-2019-1`
  - 课程编号格式：编号包含数字与字母
  - 请求加密：自定义`X-Auth-Token`请求头，自定义`X-Auth-User`响应

- 请求示例：

    ```
    {host}/teacher/2018-2019-1/3901160407
    ```

- 响应示例：

  ```json
  {
      "status": "success",
      "code": "212178",
      "name": "邓磊",
      "semester": "2018-2019-1",
      "title": "副教授",
      "unit": "软件学院",
      "available_semester": [
          "2018-2019-1",
          "2016-2017-1",
          "2016-2017-2",
          "2017-2018-1",
          "2017-2018-2",
          "2018-2019-2"
      ],
      "course": [
          {
              "name": "大型数据库技术",
              "course_code": "12E4C3DCB631491DB7F56F13873349C1",
              "room": "世B402",
              "room_code": "2430402",
              "week": [3,4,5,6,7,8,9,10],
              "week_str": "3-10/全周",
              "lesson": "10102",
              "teacher": [
                  {
                      "code": "邓磊",
                      "name": "212178",
                      "title": "副教授"
                  }
              ]
          },
          {
              "name": "云计算及应用",
              "course_code": "42654979C8F540BA9956AFF401E73F5B",
              "room": "世B402",
              "room_code": "2430402",
              "week": [11,12,13,14,15,16,17,18],
              "week_str": "11-18/全周",
              "lesson": "10102",
              "teacher": [
                  {
                      "code": "邓磊",
                      "name": "212178",
                      "title": "副教授"
                  }
              ]
          },
          ...
      ]
  }
  ```



## 服务信息

### 链接测试

- URL：`{host}/info/hello_world`

- 方式：`GET`

- 说明：

  - 学期格式形如：`2018-2019-1`
  - 课程编号格式：编号包含数字与字母

- 请求示例：

  ```
  {host}/info/hello_world
  ```

- 响应示例：

  ```json
  {
      "status": "success",
      "info": "Hello, world!"
  }
  ```

### 接口版本

- URL：`{host}/info/version`

- 方式：`GET`

- 说明：

  - 学期格式形如：`2018-2019-1`
  - 课程编号格式：编号包含数字与字母

- 请求示例：

  ```
  {host}/info/version
  ```

- 响应示例：

  ```json
  {
      "status": "success",
      "info": "线上版本：0.0.1",
      "version": "0.0.1"
  }
  ```

### 健康检查

- URL：`{host}/info/healthy`

- 方式：`GET`

- 说明：

  - 学期格式形如：`2018-2019-1`
  - 课程编号格式：编号包含数字与字母
  - 请求加密：自定义`X-Auth-Token`请求头，自定义`X-Auth-User`响应

- 请求示例：

  ```
  {host}/info/healthy
  ```

- 响应示例：

  ```json
  {
      "status": "success",
      "time": 1552675506,
      "MySQL connection": true,
      "Mongo connection": true
  }
  ```


### 数据时间

- URL：`{host}/info/data_time

- 方式：`GET`

- 说明：

  - 学期格式形如：`2018-2019-1`
  - 课程编号格式：编号包含数字与字母

- 请求示例：

  ```
  {host}/info/data_time
  ```

- 响应示例：

  ```json
  {
      "status": "success",
      "info": "2019-3-10"
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

