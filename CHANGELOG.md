### [v1.4.2](https://gitee.com/koogua/ctc-pro/releases/v1.4.2)(2026-02-10)

- 【增加】课程等类型badge区分
- 【增加】前台文章来源类型过滤
- 【增加】文档属性doc_meta_info
- 【修正】开启站点私密访问，验证码无法发送
- 【优化】精简Macros
- 【优化】首页分类课程补充一级数据
- 【优化】支付二维码页面
- 【优化】课程和文章详情页结构
- 【优化】秒杀补充专栏订单逻辑
- 【优化】用户昵称验证优化
- 【优化】用户创建以及导入
- 【优化】长课程目录自动滚动位置
- 【优化】同比和环比显示顺序

### [v1.4.1](https://gitee.com/koogua/ctc-pro/releases/v1.4.1)(2026-01-25)

- 【增加】后台文章|提问|回答发布可自定义作者
- 【增加】引入kg-label-tips
- 【增加】数据统计增加同比和环比
- 【修正】上传发票检查
- 【优化】重写$request->getClientAddress()
- 【优化】CourseUserTrait等
- 【优化】简化CheckGroupUserById等命名
- 【优化】课程|专栏|试卷学员管理
- 【优化】echarts图表输出美化
- 【优化】echarts图表类本地化
- 【优化】升级layui-v2.13.3

### [v1.4.0](https://gitee.com/koogua/ctc-pro/releases/v1.4.0)(2026-01-10)

- 【增加】分组课程|试卷|专栏
- 【增加】消息中心问题回答评论解析
- 【增加】教师详细资料
- 【修正】不存在下一课时开始学习问题
- 【优化】kindeditor编辑器初始化
- 【优化】清理多余kg-submit标记
- 【优化】赠送课程|试卷|专栏可多选
- 【优化】升级layui-v2.13.2

### [v1.3.9](https://gitee.com/koogua/ctc-pro/releases/v1.3.9)(2025-12-03)

- 【增加】切屏点播暂停，可以用来增强专注力
- 【增加】切屏考试遮罩，可以用来防止作弊
- 【修正】手机端首页基本/模块配置重置问题
- 【修正】断流再复播不能再观看直播问题
- 【优化】开启私密访问 ios 移动端返回键问题
- 【优化】用户导入增加昵称字段
- 【优化】web 端考试界面文字
- 【优化】markdown.css 中 ul>li 样式
- 【优化】js上传课件资料去掉StorageClass参数，避免存储AZ特性的问题
- 【优化】同步学习进度$cacheLearning返回类型
- 【优化】doc文档上传限制为100M
- 【优化】针对接口优化空数据返回

### [v1.3.8](https://gitee.com/koogua/ctc-pro/releases/v1.3.8)(2025-09-24)

- 增加极速高清转码
- 更新layui-v2.11.6
- 替换组件layui-tab -> layui-tabs

### [v1.3.7](https://gitee.com/koogua/ctc-pro/releases/v1.3.7)(2025-08-10)

- 优化统计菜单子项数量
- 优化获取直播信息弹窗
- 优化视频点播播放器PlayMask
- 优化course-card相关UI
- 优化layer.volt基础模板
- 优化教师直播通知发送任务
- 密码相关输入框增加可视切换

- ### [v1.3.6](https://gitee.com/koogua/ctc-pro/releases/v1.3.6)(2025-06-20)

- 增加cos文档独立配置
- 增加CloseLiveTask
- 增加搜索页图片alt属性striptags过滤
- 后台增加返回顶部快捷方式
- 前台fixbar增加联系电话
- 优化安装脚本
- 优化ThrottleLimit和LoginLimit
- 优化课时列表直播提示
- 优化最后学习课时获取
- 优化后台返回链接
- 优化统计分析代码位置
- 修正前台直播管理设置问题
- 直播回调后更新课时缓存
- 后台清空头像->上传头像
- sitemap.xml直接写入网站根目录

- ### [v1.3.5](https://gitee.com/koogua/ctc-pro/releases/v1.3.5)(2025-04-20)
- 
- 优化索引管理工具
- 优化章节评价等UI
- 优化收藏点赞交互
- 修正workerman中onMessage问题
- method="post" -> method="POST"
- 修正非免费课程试听问题
- 优化layer窗口中的表单跳转
- 文件清理以及命名优化
- 优化倒计时

### [v1.3.4](https://gitee.com/koogua/ctc-pro/releases/v1.3.4)(2025-03-22)

- 升级layui-v2.9.25
- 大幅优化重构优惠券
- 去除文章和问题缓存重建
- 修正每日访问站点积分问题
- 限制全文搜索关键字长度
- 统一规划二维码样式

### [v1.3.3](https://gitee.com/koogua/ctc-pro/releases/v1.3.3)(2025-02-22)

- 优化后台统计图表
- 优化图片放大查看
- 优化错误处理机制
- 优化前台编辑器页面
- 去除一些过度的设计
- 精简属性空判断
- 规整redirect
- 优化bootstrap
- 优化logger
- 优化contact
- 优化logo
- 优化nav

### [v1.3.2](https://gitee.com/koogua/ctc-pro/releases/v1.3.2)(2024-12-10)

- 更新layui-v2.9.20
- 优化发票录入
- 优化课程期限
- 优化编辑器内容自动提交
- 联系方式增加抖音二维码
- 修正课时详情页目录高亮问题
- 修正CommentInfo中点赞判断
- 精简AccountSearchTrait
- 优化kg_h5_index_url()
- 优化CourseUserTrait
- 优化kg_setting()
- 优化CsrfToken

### [v1.3.1](https://gitee.com/koogua/ctc-pro/releases/v1.3.1)(2024-09-31)

- 增加删除COS原始视频功能
- 增加跳转外部URL安全提示
- 增加编辑器内容自动保存
- 更新layui-v2.9.16
- 优化findUserActiveSession和findUserActiveTokens
- 优化文章和提问最大可用tag数量
- 优化用户锁定相关
- 优化错误日志记录
- 修正优惠劵最低消费额匹配问题

### [v1.3.0](https://gitee.com/koogua/ctc-pro/releases/v1.3.0)(2024-07-31)

- 升级layui-v2.9.14
- 增加人工模拟组卷统计分析
- 后台增加客户服务入口
- 修正教师直播通知
- 修正邮件内容格式化
- 增加阅卷完成通知配置
- 增加视频点播学时防刷验证
- 增加log.trace参数优化日志
- 增加登录失败锁定
- redis增加expire方法
- 优化第三方登录最后时间
- 优化公众号订阅
- 精简代码等

### [v1.2.9](https://gitee.com/koogua/ctc-pro/releases/v1.2.9)(2024-06-31)

- 更新layui-v2.9.10
- 更新docker国内镜像地址
- 增加导入镜像构建容器的方式
- 教师不批阅非首次考试试卷
- 补充model为文档课程的交互提醒和说明
- 文档类型课时补充文件大小扩展属性
- 答案菜单下增加评论管理链接
- 修正积分兑换实物发货链接
- license页面增加icp备案指向链接
- 轮播图增加专栏类型目标链接
- 增加课程能否发布检查
- 去除初始化kindeditor语言文件
- 去除选择题EF选项
- 优化富文本内容显示样式
- 优化内容图片点击放大监听
- 优化试题题干答案等图片样式
- 优化课时为图文时默认格式
- 优化公众号关注和取关逻辑
- 优化macros
- 优化后台页面

### [v1.2.8](https://gitee.com/koogua/ctc-pro/releases/v1.2.8)(2024-06-1)

- 增加文档类型课时（在线预览word,ppt,pdf等）
- 第三方账号登录增加账号可用性检查
- 优化文件上传mimeType检查
- 优化后台UploadController
- 优化内容图片点击放大监听
- 优化富文本内容显示
- 优化简答题附件上传
- 修正支付回调中buyer_id为null的情况
- 修正Question查询中参数重复问题
- 修正队列任务保持数据库活跃问题
- 修正最后学习课时问题
- 增加后台打开关闭左侧菜单提示
- iconfont资源本地化
- 调整公众号模板消息
- 增加积分礼品H5跳转
- 支付限额1w->10w
- 更新layui-v2.9.9

### [v1.2.7](https://gitee.com/koogua/ctc-pro/releases/v1.2.7)(2024-05-15)

- 更新layui-v2.9.8
- 清理无用的Captcha配置
- 导入模板文件本地存储
- QQ联系方式改为二维码图片
- 使用第三方播放器播放加密视频
- 优化关注公众号页面逻辑
- 调整html编辑器属性
- 修正logo,favicon上传返回路径
- 钉钉和企业微信通知中增加用户手机号参数
- 单元练习自动触发刷新获取试题
- 存在待阅试卷，不能再次考试
- 增加试题批量发布和删除
- 增加试题通过率展示
- 增加pre内容复制功能
- 优化试题挑错布局

### [v1.2.6](https://gitee.com/koogua/ctc-pro/releases/v1.2.6)(2024-04-15)

- 增加试题挑错
- 增加删除和还原用户
- 增加图文和试卷背景水印
- 优化待阅试卷
- 优化uploadTempFile临时文件上传
- 优化CategoryTreeListBuilder
- 优化ArticleUserTrait等
- 还原误删的QuestionXmCategories
- 修正ServerMonitorTask传参问题
- 修正chapter_user中学习时间重复计数问题
- 更改connect表中open_avatar字段长度为255
- 后台用户链接变更：admin.user.show->home.user.show
- 更新layui-v2.9.7

### [v1.2.5](https://gitee.com/koogua/ctc-pro/releases/v1.2.5)(2024-03-01)

- 图文课时以及专栏内容增加markdown格式支持
- 单元练习测试增加题型过滤
- 增加post方式传递csrf_token
- 增加图文内容图片样式处理
- 优化Storage类

### [v1.2.4](https://gitee.com/koogua/ctc-pro/releases/v1.2.4)(2024-01-30)

- 修正course.exam_papers路由
- 修正用户active_time搜索条件
- courseExamPaperList添加若干属性
- 使用xlswriter替换phpspreadsheet
- 使用AccountSearchTrait复用代码
- 增加主观题上传图片和人工判卷评分
- 修正chapter_user表plan_id=0问题
- 去除league/commonmark包
- 更新layui-v2.9.3
- 补充文章优惠码相关
- 优化分类等必选判断
- 增加订单和提现导出
- 优化错误处理

### [v1.2.3](https://gitee.com/koogua/ctc-pro/releases/v1.2.3)(2023-12-15)

- 增加专栏分类功能
- 增加问题分类功能
- 增加专栏付费功能
- 增加审核等批量功能
- 增加若干业务插件埋点
- 精简重构大量业务逻辑
- 已发现的问题修复

### [v1.2.2](https://gitee.com/koogua/ctc-pro/releases/v1.2.2)(2023-09-01)

- 消费队列常驻任务增加保持数据库链接逻辑
- 课程和时间增加标签属性
- 修正邮箱注册提交按钮不可用问题
- 去除删除远程COS文件逻辑
- 优化课程课件资料相关逻辑
- 修正后台添加问题标签为空报错问题
- 修正课程最后学习计算duration未定义问题
- 优化ExamPaperUserTrait

### [v1.2.1](https://gitee.com/koogua/ctc-pro/releases/v1.2.1)(2023-07-15)

- 增加开票额度收支逻辑
- 使用本地图像验证码
- 升级layui-v2.8.8
- 优化钉钉webhook
- 优化队列处理任务
- 修正提现平台检查
- 修正移动端配置项为空异常
- 调整UserBalance属性

### [v1.2.0](https://gitee.com/koogua/ctc-pro/releases/v1.2.0)(2023-06-15)

 - 增加beanstalk消费队列配套功能
 - 修正优惠券Macro endif不匹配问题
 - 修正创建提现账号问题
 - 优化OfficeAccount语法结构
 - 优化提现账号验证流程
 - 优化课程等MeInfo信息
 - 优化外部通知

### [v1.1.9](https://gitee.com/koogua/ctc-pro/releases/v1.1.9)(2023-05-31)

- 增加试卷全文检索
- 增加课程最后学习API
- 增加专栏文章付费
- 增加专栏学习记录
- 增加推荐课程|文章|考试widget
- 加强废除兑换码功能
- 去除课程打赏功能
- 错题|收藏题增加题型过滤
- 考试多选题漏选不得分
- 更新LayUI2.8.2
- 修正登录空口令问题
- 修正PageTrait中单词拼写错误
- 优化后台Setting更新
- 优化订单确认页样式
- 优化me相关信息
- 优化用户中心菜单
- 优化分享URL
- 优化开票处理

### [v1.1.8](https://gitee.com/koogua/ctc-pro/releases/v1.1.8)(2023-05-06)

- 后台试卷增加标题搜索
- 课时增加评论|弹幕|快进控制
- 练习增加重新开始功能
- 修正题帽题收藏样式
- 修正练习人工组卷换题
- 练习增加我的得分显示
- 拼团倒计时改为时间段显示
- 增加热卖试卷统计
- 增加虚拟成团配置
- 增加弹幕功能
- 增加右键屏蔽功能
- 优化文章|课程|试卷结构

### [v1.1.7](https://gitee.com/koogua/ctc-pro/releases/v1.1.7)(2023-04-06)

- 增加同步练习
- 增加错题练习
- 增加收藏练习
- 增加移动端配置
- 其他考试优化
- 其他bug修复

### [v1.1.6](https://gitee.com/koogua/ctc-pro/releases/v1.1.6)(2023-03-17)

- 增加刷新opcache功能
- 增加领券中心|分销|秒杀|的移动端跳转
- 增加后台产品动态关闭开关
- 增加咨询置顶功能
- 增加学习证书相关api
- 增加考试收藏
- 格式化团购价格
- 首页轮播图+课程合并为首页缓存
- 强化文章|问题|课程列表参数检查
- 后台退款比例可任意调节
- 去除“新鲜”订单检查
- 修正团购订单错位问题
- 修正vip订单退款问题
- 优化排序条件，改善分页重复问题
- 优化清理测试数据脚本
- 优化通用数据验证器
- 优化已赞查询判断
- 优化学习时长同步
- 优化优惠券

### [v1.1.5](https://gitee.com/koogua/ctc-pro/releases/v1.1.5)(2023-02-11)

- 增加荣誉证书功能
- 增加后台添加课程评价功能
- 增加内容标签接口
- 增加h5和static目录自动同步到腾讯云存储
- 增加ServerMonitor监控指标配置
- Answer增加closed属性
- 文章和问答增加allow_comment属性
- 修正视频记忆播放无效问题
- 修正评论回复发布参数问题
- 优化二维码输出
- 优化海报输出
- 优化授权期限提醒
- 优化管理后台细节
- 优化考试分类查询条件
- 优化Repo查询默认排序
- 优化PaperUser细节
- 升级腾讯云播放器
- 升级加密播放签名算法
- 同步更新腾讯云短信内容规则
- 优化评分检查
- 优化命令行任务基类中获取子类名
- 优化分布式执行任务锁定

### [v1.1.4](https://gitee.com/koogua/ctc-pro/releases/v1.1.4)(2022-12-12)

- 增加赠送优惠券到个人账户
- 文章|问题|回答增加附加图片（用于移动端）
- CLI终端任务适应分布式部署
- 同步索引任务适用分布式部署
- 站点配置接口过滤敏感项
- 修正播放器中央按钮显示问题
- 修正添加套餐summary未定义问题
- 修正用户通知标记为已读，计数不归零问题
- 优化API基类和Home基类
- 优化ServerInfo类

### [v1.1.3](https://gitee.com/koogua/ctc-pro/releases/v1.1.3)(2022-11-13)

- 增加站点公开访问配置
- 增强播放地址安全
- 编辑器支持粘贴图片
- 编辑器支持远程图片本地化
- 修正当月在线统计
- 修正发票上传权限问题
- 修正发票下载问题
- 修正考试人数重复计数
- 增加开票导出
- 增加开票用户邮件通知
- 优化优惠码和学员导出
- 优化表单数据提交体验
- 优化腾讯云播放地址鉴权参数
- 优化单章节层级显示
- 优化热门作者
- 优化热门答主
- 优化热门问题

### [v1.1.2](https://gitee.com/koogua/ctc-pro/releases/v1.1.2)(2022-10-26)

- 增加课程用户导出
- 增加考试用户和考试记录导出
- 增加提现和开票等接口
- 播放器中间增加大号播放按钮
- 单页和帮助增加浏览计数属性
- 后台增加查看考试答题详情
- 后台用户详情页增加订单，分销团队，收支明细
- 课程和章节增加最近学习属性
- logo上增加首页链接
- 修正分类默认图标问题
- 修正优惠券不可用问题
- 修正layui-main样式更新带来的问题
- 修正开源版升级付费版迁移文件不执行问题
- 使用tab页方式布局专题和套餐编辑页
- 整理业务查询库，优化后台搜索页面
- 更新composer包
- 调整退款手续费范围
- 导航部分，教师->师资
- 优化分页组件参数
- 优化城建提现账号以及验证
- 优化热门问题和热门答主
- 优化通知计数方式
- 优化子题排序

### [v1.1.1](https://gitee.com/koogua/ctc-pro/releases/v1.1.1)(2022-09-06)

- 去除user全文索引
- 修正订单确认页优惠券列表错误
- 修正编辑器内容图片上传
- 调整notice目录结构
- 更新默认图片
- 更新直播名称格式
- 优化专题信息
- 优化router扫描
- 升级layui v2.7.6
- 增加用户协议和隐私政策
- 优化错误日志

### [v1.1.0](https://gitee.com/koogua/ctc-pro/releases/v1.1.0)(2022-09-06)

- 优化用户登录，用户注册，忘记密码
- 修复移动端首页课程缓存刷新问题
- 修正cos-sdk升级带来的参数检查问题
- 修正随机组卷不能发布的问题
- 修正随机组卷属性问题  
- 修正直播websocket心跳
- 轮播图增加试卷类型支持
- sitemap条目增加过滤条件
- composer.json增加allow_plugins配置项
- 更新授权域名链接
- 更新移动端跳转链接  
- 增加分销成功通知
- 增加内容自动审核
- 整理migrations
- 整理权限

### [v1.0.9](https://gitee.com/koogua/ctc-pro/releases/v1.0.9)(2022-08-18)

- 清理IM残留
- 清理群组残留
- 优化试题搜索
- 增加clean_html过滤器
- 增加随机组卷模式
- 增加分享登录判断
- 精简文章内容渲染css
- 修复点击考试记录无响应
- 升级腾讯云存储SDK到v2.5.6
- GuzzleHttp升级到v6.5.7
- 优化腾讯云短信错误日志
- 优化HtmlPurifier缓存目录自动创建
- 优化整理CSS

### [v1.0.8](https://gitee.com/koogua/ctc-pro/releases/v1.0.8)(2022-08-08)

- 增加去版权判断
- 增加应用内命令行数据迁移
- 移除群组和微聊模块
- 优化评价和咨询搜索
- 优化课程学习和考试记录
- 优化筛选和添加试题
- 使用Trait精简Service基类
- 使用kindeditor替换vditor
- markdown转html

### [v1.0.7](https://gitee.com/koogua/ctc-pro/releases/v1.0.7)(2022-07-21)

- 修正metadata缓存库选择问题
- 修正用户锁定期限内还可以登录问题
- 修正批量导入大量用户内存溢出问题  
- 优化订单交易退款搜索  
- 优化模块注册和路由扫描
- 优化CsrfToken
- 优化在线考试  
- 引入HtmlPurifier
- 增加清理试题脚本  
- 增加清理demo数据脚本
- 增加关闭考试计划任务  
- 增加试题收藏
- 增加试题防复制  

### [v1.0.6](https://gitee.com/koogua/ctc-pro/releases/v1.0.6)(2022-07-08)

- 初步完成题库和考试
- 优化退出终端用户
- 后台用户列表增加手机和邮箱属性
- 调整页面结构和样式

### [v1.0.5](https://gitee.com/koogua/ctc-pro/releases/v1.0.5)(2022-06-28)

- 增加用户批量导入
- 增加微信公众号扫码登录
- 增加开始学习功能
- 增加系统安全配置
- 增加电子执照，ISP许可配置
- 增加腾讯云视频ID支持
- 优化验证码参数
- 优化拼团详情封面样式
- 优化课程以及套餐发货
- 优化点播上传转码流程
- 修正二级分类查询条件
- 修正电子兑换卡导出问题  
- 完善积分兑换会员
- 完善标签查询规则

### [v1.0.4](https://gitee.com/koogua/ctc-pro/releases/v1.0.4)(2022-05-28)

- 增加授权到期提醒
- 增加图形验证码接口
- 增加秒杀，团购，积分商城，电子兑换接口
- vditor编辑器切换为七牛cdn加速
- 优化章节排序初始值和步长
- 整理积分礼品类型

### [v1.0.3](https://gitee.com/koogua/ctc-pro/releases/v1.0.3)(2022-04-28)

- 增加直播录制回放功能
- 增加直播间管理功能
- 修正课时移动端跳转问题

### [v1.0.2](https://gitee.com/koogua/ctc-pro/releases/v1.0.2)(2022-04-18)

- 增加开票相关功能
- 收货地址支持多个地址
- 调整积分礼品兑换相关逻辑
- 用户中心增加月积分统计图表
- 修复秒杀订单关闭后没有回退库存问题

### [v1.0.1](https://gitee.com/koogua/ctc-pro/releases/v1.0.1)(2022-03-18)

- 修复后台点播设置码率500的错误
- 修复第三方登录解绑问题
- 修复套餐兑换没有分配课程问题
- 修复分销和拼团字段不一致问题
- 修复升级脚本中提现完成短信模板问题
- 修复大小写导致的微信登录通知失败问题  
- 优化价格金额限制
- 优化文件和目录命名  
- 文章问题单页帮助增加关键字属性
- 专题增加封面属性
- 加强课程附件下载权限检查
- 增加验证码开启开关
- 课程支持点播直播图文等混用

### [v1.0.0](https://gitee.com/koogua/ctc-pro/releases/v1.0.0)(2022-01-13)
