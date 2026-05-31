<?php
/**
 * @copyright Copyright (c) 2021 深圳市文联软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

$error = [];

/**
 * 系统相关
 */
$error['sys.unauthorized'] = '认证失败';
$error['sys.forbidden'] = '拒绝访问';
$error['sys.bad_request'] = '无效的请求';
$error['sys.not_found'] = '资源不存在';
$error['sys.server_error'] = '服务器内部错误';
$error['sys.service_unavailable'] = '服务不可用';
$error['sys.trans_rollback'] = '事务回滚';
$error['sys.unknown_error'] = '未知错误';

/**
 * 安全相关
 */
$error['security.too_many_requests'] = '请求过于频繁';
$error['security.invalid_csrf_token'] = '无效的CSRF令牌';
$error['security.invalid_http_referer'] = '无效请求来源';
$error['security.client_address_blocked'] = '客户端地址被禁止';

/**
 * 验证相关
 */
$error['verify.invalid_phone'] = '无效手机号';
$error['verify.invalid_email'] = '无效的邮箱';
$error['verify.invalid_code'] = '无效的验证码';
$error['verify.invalid_sms_code'] = '无效的短信验证码';
$error['verify.invalid_mail_code'] = '无效的邮件验证码';
$error['verify.send_sms_failed'] = '发送短信失败';
$error['verify.send_mail_failed'] = '发送邮件失败';

/**
 * captcha相关
 */
$error['captcha.invalid_code'] = '无效的验证码';

/**
 * 帐号相关
 */
$error['account.not_found'] = '账号不存在';
$error['account.locked'] = '账号被锁定，无法登录';
$error['account.too_many_login_attempts'] = '登录失败次数过多，已暂停登录，请稍后再试！';
$error['account.invalid_login_name'] = '无效的登录名';
$error['account.invalid_email'] = '无效的电子邮箱';
$error['account.invalid_phone'] = '无效的手机号';
$error['account.invalid_pwd'] = '无效的密码（字母|数字|特殊字符6-16位）';
$error['account.email_taken'] = '邮箱被占用';
$error['account.phone_taken'] = '手机号被占用';
$error['account.pwd_not_match'] = '密码不匹配';
$error['account.origin_pwd_incorrect'] = '原有密码不正确';
$error['account.login_pwd_incorrect'] = '登录密码不正确';
$error['account.register_disabled'] = '注册已关闭';
$error['account.register_with_phone_disabled'] = '手机注册已关闭';
$error['account.register_with_email_disabled'] = '邮箱注册已关闭';

/**
 * 用户相关
 */
$error['user.not_found'] = '用户不存在';
$error['user.name_taken'] = '昵称被占用';
$error['user.title_too_long'] = '头衔过长（超过30个字符）';
$error['user.about_too_long'] = '简介过长（超过255个字符）';
$error['user.profile_too_long'] = '资料过长（超过10000个字符）';
$error['user.invalid_name'] = '无效的昵称（汉字|字母|数字，2-15个字符）';
$error['user.invalid_gender'] = '无效的性别类型';
$error['user.invalid_area'] = '无效的省市地区';
$error['user.invalid_avatar'] = '无效的头像';
$error['user.invalid_edu_role'] = '无效的教学角色';
$error['user.invalid_edu_role_label'] = '自定义教学角色名称不能超过4个字';
$error['user.invalid_admin_role'] = '无效的后台角色';
$error['user.invalid_vip_status'] = '无效的会员状态';
$error['user.invalid_vip_expiry_time'] = '无效的会员期限';
$error['user.invalid_lock_status'] = '无效的锁定状态';
$error['user.invalid_lock_expiry_time'] = '无效的锁定期限';

/**
 * 用户导入
 */
$error['user_import.too_many_rows'] = '导入数据过多（超过5000行）';
$error['user_import.exceed_licensed_user_count'] = '超出授权用户数量';

/**
 * 分类相关
 */
$error['category.not_found'] = '分类不存在';
$error['category.parent_not_found'] = '父级分类不存在';
$error['category.invalid_type'] = '无效的分类类型';
$error['category.invalid_icon'] = '无效的分类图标';
$error['category.invalid_priority'] = '无效的排序值（范围：1-255）';
$error['category.invalid_publish_status'] = '无效的发布状态';
$error['category.name_too_short'] = '名称太短（少于2个字符）';
$error['category.name_too_long'] = '名称太长（多于30个字符）';
$error['category.has_child_node'] = '不允许相关操作（存在子节点）';

/**
 * 标签相关
 */
$error['tag.not_found'] = '标签不存在';
$error['tag.invalid_icon'] = '无效的图标';
$error['tag.invalid_priority'] = '无效的排序值（范围：1-255）';
$error['tag.invalid_publish_status'] = '无效的发布状态';
$error['tag.name_too_short'] = '名称太短（少于2个字符）';
$error['tag.name_too_long'] = '名称太长（多于30个字符）';

/**
 * 导航相关
 */
$error['nav.not_found'] = '导航不存在';
$error['nav.parent_not_found'] = '父级分类不存在';
$error['nav.invalid_url'] = '无效的访问地址';
$error['nav.invalid_position'] = '无效的位置类型';
$error['nav.invalid_target'] = '无效的目标类型';
$error['nav.invalid_priority'] = '无效的排序值（范围：1-255）';
$error['nav.invalid_publish_status'] = '无效的发布状态';
$error['nav.name_too_short'] = '名称太短（少于2个字符）';
$error['nav.name_too_long'] = '名称太长（多于30个字符）';
$error['nav.has_child_node'] = '不允许相关操作（存在子节点）';

/**
 * 文章相关
 */
$error['article.not_found'] = '文章不存在';
$error['article.title_too_short'] = '标题太短（少于2个字符）';
$error['article.title_too_long'] = '标题太长（多于50个字符）';
$error['article.summary_too_long'] = '摘要太长（多于255个字符）';
$error['article.content_too_short'] = '内容太短（少于10个字符）';
$error['article.content_too_long'] = '内容太长（多于60000个字符）';
$error['article.invalid_source_type'] = '无效的来源类型';
$error['article.invalid_source_url'] = '无效的来源网址';
$error['article.invalid_user_count'] = '无效的订阅数量（范围：0-999999）';
$error['article.invalid_market_price'] = '无效的市场价格（范围：0-999999）';
$error['article.invalid_vip_price'] = '无效的会员价格（范围：0-999999）';
$error['article.invalid_study_expiry'] = '无效的学习期限';
$error['article.invalid_feature_status'] = '无效的推荐状态';
$error['article.invalid_publish_status'] = '无效的发布状态';
$error['article.invalid_close_status'] = '无效的关闭状态';
$error['article.invalid_reject_reason'] = '无效的拒绝理由';
$error['article.edit_not_allowed'] = '当前不允许编辑文章';
$error['article.delete_not_allowed'] = '当前不允许删除文章';

/**
 * 问答相关
 */
$error['question.not_found'] = '问题不存在';
$error['question.title_too_short'] = '标题太短（少于5个字符）';
$error['question.title_too_long'] = '标题太长（多于50个字符）';
$error['question.summary_too_long'] = '摘要太长（多于255个字符）';
$error['question.content_too_short'] = '内容太短（少于10个字符）';
$error['question.content_too_long'] = '内容太长（多于10000个字符）';
$error['question.invalid_publish_status'] = '无效的发布状态';
$error['question.invalid_reject_reason'] = '无效的拒绝理由';
$error['question.edit_not_allowed'] = '当前不允许编辑问题';
$error['question.delete_not_allowed'] = '当前不允许删除问题';

$error['answer.not_found'] = '回答不存在';
$error['answer.content_too_short'] = '内容太短（少于10个字符）';
$error['answer.content_too_long'] = '内容太长（多于10000个字符）';
$error['answer.invalid_reject_reason'] = '无效的拒绝理由';
$error['answer.post_not_allowed'] = '当前不允许发布回答';
$error['answer.edit_not_allowed'] = '当前不允许编辑回答';
$error['answer.delete_not_allowed'] = '当前不允许删除回答';

/**
 * 评论相关
 */
$error['comment.not_found'] = '评论不存在';
$error['comment.parent_not_found'] = '上级评论不存在';
$error['comment.invalid_item_type'] = '无效的条目类型';
$error['comment.invalid_publish_status'] = '无效的发布状态';
$error['comment.invalid_reject_reason'] = '无效的拒绝理由';
$error['comment.content_too_short'] = '内容太短（少于2个字符）';
$error['comment.content_too_long'] = '内容太长（多于1000个字符）';

/**
 * 课程相关
 */
$error['course.not_found'] = '课程不存在';
$error['course.title_too_short'] = '标题太短（少于5个字符）';
$error['course.title_too_long'] = '标题太长（多于50个字符）';
$error['course.summary_too_long'] = '标题太长（多于255个字符）';
$error['course.keyword_too_long'] = '关键字太长（多于100个字符）';
$error['course.detail_too_long'] = '详情太长（多于5000个字符）';
$error['course.invalid_model'] = '无效的模型类别';
$error['course.invalid_level'] = '无效的难度级别';
$error['course.invalid_cover'] = '无效的封面';
$error['course.invalid_user_count'] = '无效的订阅数量（范围：0-999999）';
$error['course.invalid_market_price'] = '无效的市场价格（范围：0-999999）';
$error['course.invalid_vip_price'] = '无效的会员价格（范围：0-999999）';
$error['course.invalid_study_expiry'] = '无效的学习期限';
$error['course.invalid_refund_expiry'] = '无效的退款期限';
$error['course.invalid_feature_status'] = '无效的推荐状态';
$error['course.invalid_publish_status'] = '无效的发布状态';
$error['course.content_not_ready'] = '课程内容未就绪';
$error['course.import_template_not_existed'] = '课目导入模板文件不存在';
$error['course.duplicate'] = '可能存在重复课程';

/**
 * 面授课程相关
 */
$error['course_offline.invalid_start_date'] = '无效的开始日期';
$error['course_offline.invalid_end_date'] = '无效的结束日期';
$error['course_offline.invalid_time_range'] = '无效的起始区间';
$error['course_offline.invalid_user_limit'] = '无效的用户限额（范围：1-999）';
$error['course_offline.invalid_location'] = '无效的上课地点（范围10-50字符）';

/**
 * 话题相关
 */
$error['topic.not_found'] = '话题不存在';
$error['topic.title_too_short'] = '标题太短（少于2个字符）';
$error['topic.title_too_long'] = '标题太长（多于50个字符）';
$error['topic.summary_too_long'] = '简介太长（多于255个字符）';
$error['topic.invalid_publish_status'] = '无效的发布状态';

/**
 * 证书相关
 */
$error['certificate.not_found'] = '证书不存在';
$error['certificate.name_too_short'] = '证书名称太短（少于2个字符）';
$error['certificate.name_too_long'] = '证书名称太长（多于30个字符）';
$error['certificate.invalid_item_type'] = '无效的证书类型';
$error['certificate.invalid_grant_type'] = '无效的授予方式';
$error['certificate.invalid_publish_status'] = '无效的发布状态';
$error['certificate.item_not_bound'] = '所选专题 / 课程 / 考试未配置证书';
$error['certificate.manual_grant_not_allowed'] = '当前证书不支持手动发放';

$error['cert_user.not_found'] = '证书授予记录不存在';
$error['cert_user.already_granted'] = '该用户已授予所选证书';

/**
 * 套餐相关
 */
$error['package.not_found'] = '套餐不存在';
$error['package.title_too_short'] = '标题太短（少于5个字符）';
$error['package.title_too_long'] = '标题太长（多于50个字符）';
$error['package.summary_too_long'] = '简介太长（多于255个字符）';
$error['package.invalid_cover'] = '无效的封面';
$error['package.invalid_market_price'] = '无效的市场价格（范围：1-999999）';
$error['package.invalid_vip_price'] = '无效的会员价格（范围：1-999999）';
$error['package.invalid_publish_status'] = '无效的发布状态';

/**
 * 会员相关
 */
$error['vip.not_found'] = '会员不存在';
$error['vip.title_too_short'] = '标题太短（少于5个字符）';
$error['vip.title_too_long'] = '标题太长（多于30个字符）';
$error['vip.invalid_price'] = '无效的价格（范围：1-999999）';
$error['vip.invalid_expiry'] = '无效的期限（范围：1~60）';

/**
 * 课程成员
 */
$error['course_user.not_found'] = '课程学员关系不存在';
$error['course_user.invalid_expiry_time'] = '无效的过期时间';
$error['course_user.review_not_allowed'] = '当前不允许评价课程';
$error['course_user.has_reviewed'] = '已经评价过该课程';

/**
 * 章节相关
 */
$error['chapter.not_found'] = '章节不存在';
$error['chapter.parent_not_found'] = '父级章节不存在';
$error['chapter.invalid_model'] = '无效的课目类型';
$error['chapter.invalid_priority'] = '无效的排序值（范围：1-255）';
$error['chapter.invalid_free_status'] = '无效的免费状态';
$error['chapter.invalid_publish_status'] = '无效的发布状态';
$error['chapter.title_too_short'] = '标题太短（少于2个字符）';
$error['chapter.title_too_long'] = '标题太长（多于30个字符）';
$error['chapter.summary_too_long'] = '简介太长（多于255个字符）';
$error['chapter.vod_not_ready'] = '点播资源尚未就绪';
$error['chapter.read_not_ready'] = '文章内容尚未就绪';
$error['chapter.doc_not_ready'] = '文档内容尚未就绪';
$error['chapter.live_time_empty'] = '直播时间尚未设置';
$error['chapter.offline_time_empty'] = '面授时间尚未设置';
$error['chapter.child_existed'] = '不允许相关操作（存在子章节）';

/**
 * 点播相关
 */
$error['chapter_vod.not_found'] = '点播资源不存在';
$error['chapter_vod.invalid_trans_mode'] = '无效的转码模式';
$error['chapter_vod.invalid_duration'] = '无效的视频时长';
$error['chapter_vod.invalid_file_id'] = '无效的文件编号';
$error['chapter_vod.invalid_file_url'] = '无效的文件地址';
$error['chapter_vod.invalid_file_ext'] = '无效的文件格式（目前只支持mp4，m3u8）';
$error['chapter_vod.remote_file_required'] = '请填写远程播放地址';

/**
 * 弹幕相关
 */
$error['danmu.not_found'] = '弹幕资源不存在';
$error['danmu.invalid_type'] = '无效的弹幕类型';
$error['danmu.invalid_time'] = '无效的弹幕时间轴';
$error['danmu.invalid_color'] = '无效的弹幕颜色';
$error['danmu.invalid_size'] = '无效的弹幕字号（范围：12-36）';
$error['danmu.text_too_short'] = '弹幕内容太短（少于1字符）';
$error['danmu.text_too_long'] = '弹幕内容太长（多于50字符）';

/**
 * 直播相关
 */
$error['chapter_live.not_found'] = '直播资源不存在';
$error['chapter_live.invalid_start_time'] = '无效的开始时间';
$error['chapter_live.invalid_end_time'] = '无效的结束时间';
$error['chapter_live.invalid_time_range'] = '无效的起始区间';
$error['chapter_live.time_too_long'] = '直播时间太长（超过3小时）';

$error['live_block.not_found'] = '黑名单不存在';
$error['live_block.invalid_expiry'] = '无效的过期时间';

$error['live_chat.msg_too_short'] = '消息过短（少于1个字符）';
$error['live_chat.msg_too_long'] = '消息过长（多于255个字符）';
$error['live_chat.no_manage_priv'] = '没有管理权限';
$error['live_chat.chat_disabled'] = '讨论已关闭';
$error['live_chat.user_blocked'] = '用户已禁言';

/**
 * 图文相关
 */
$error['chapter_read.not_found'] = '文章不存在';
$error['chapter_read.content_too_short'] = '文章内容太短（少于10个字符）';
$error['chapter_read.content_too_long'] = '文章内容太长（多于60000个字符）';

/**
 * 面授相关
 */
$error['chapter_offline.invalid_start_time'] = '无效的开始时间';
$error['chapter_offline.invalid_end_time'] = '无效的结束时间';
$error['chapter_offline.invalid_time_range'] = '无效的起始区间';

/**
 * 评价相关
 */
$error['review.not_found'] = '评价不存在';
$error['review.invalid_rating'] = '无效的评分（范围：1-5）';
$error['review.invalid_anonymous_status'] = '无效的匿名状态';
$error['review.invalid_publish_status'] = '无效的发布状态';
$error['review.content_too_short'] = '评价内容太短（少于10个字符）';
$error['review.content_too_long'] = '评价内容太长（多于255个字符）';
$error['review.edit_not_allowed'] = '当前不允许修改操作';
$error['review.has_liked'] = '你已经点过赞啦';

/**
 * 咨询相关
 */
$error['consult.not_found'] = '咨询不存在';
$error['consult.invalid_rating'] = '无效的评分（范围：1-5）';
$error['consult.invalid_private_status'] = '无效的私密状态';
$error['consult.invalid_sticky_status'] = '无效的置顶状态';
$error['consult.invalid_publish_status'] = '无效的发布状态';
$error['consult.question_duplicated'] = '你已经咨询过类似问题啦';
$error['consult.question_too_short'] = '问题内容太短（少于5个字符）';
$error['consult.question_too_long'] = '问题内容太长（多于1000个字符）';
$error['consult.answer_too_short'] = '回复内容太短（少于5个字符）';
$error['consult.answer_too_long'] = '回复内容太长（多于1000个字符）';
$error['consult.edit_not_allowed'] = '当前不允许修改操作';
$error['consult.has_liked'] = '你已经点过赞啦';

/**
 * 单页相关
 */
$error['page.not_found'] = '单页不存在';
$error['page.title_too_short'] = '标题太短（少于2个字符）';
$error['page.title_too_long'] = '标题太长（多于50个字符）';
$error['page.alias_too_short'] = '别名太短（少于2个字符）';
$error['page.alias_too_long'] = '别名太长（多于50个字符）';
$error['page.content_too_short'] = '内容太短（少于10个字符）';
$error['page.content_too_long'] = '内容太长（多于10000个字符）';
$error['page.keyword_too_long'] = '关键字太长（多于100个字符）';
$error['page.invalid_alias'] = '无效的别名（推荐使用英文作为别名）';
$error['page.invalid_publish_status'] = '无效的发布状态';

/**
 * 帮助相关
 */
$error['help.not_found'] = '帮助不存在';
$error['help.title_too_short'] = '标题太短（少于2个字符）';
$error['help.title_too_long'] = '标题太长（多于50个字符）';
$error['help.content_too_short'] = '内容太短（少于10个字符）';
$error['help.content_too_long'] = '内容太长（多于10000个字符）';
$error['help.keyword_too_long'] = '关键字太长（多于100个字符）';
$error['help.invalid_priority'] = '无效的排序数值（范围：1-255）';
$error['help.invalid_publish_status'] = '无效的发布状态';

/**
 * 轮播相关
 */
$error['slide.not_found'] = '轮播不存在';
$error['slide.invalid_platform'] = '无效的平台类型';
$error['slide.invalid_target'] = '无效的目标类型';
$error['slide.invalid_link'] = '无效的链接地址';
$error['slide.invalid_priority'] = '无效的排序数值（范围：1-255）';
$error['slide.invalid_cover'] = '无效的封面';
$error['slide.invalid_publish_status'] = '无效的发布状态';
$error['slide.title_too_short'] = '标题太短（少于2个字符）';
$error['slide.title_too_long'] = '标题太长（多于50个字符）';
$error['slide.summary_too_long'] = '简介太长（多于255个字符）';

/**
 * 订单相关
 */
$error['order.not_found'] = '订单不存在';
$error['order.invalid_item_type'] = '无效的商品类型';
$error['order.invalid_amount'] = '无效的支付金额';
$error['order.invalid_status'] = '无效的状态类型';
$error['order.is_delivering'] = '已经下过单了，正在准备发货中';
$error['order.has_bought_course'] = '已经报名过该课程';
$error['order.has_bought_package'] = '已经报名过该套餐';
$error['order.has_bought_exam_paper'] = '已经购买过该试卷';
$error['order.has_bought_article'] = '已经购买过该专栏';
$error['order.pay_not_allowed'] = '当前不允许支付订单';
$error['order.cancel_not_allowed'] = '当前不允许取消订单';
$error['order.refund_not_allowed'] = '当前不允许申请退款';
$error['order.refund_not_supported'] = '该品类不支持退款';
$error['order.refund_request_existed'] = '退款申请已经存在';

/**
 * 交易相关
 */
$error['trade.not_found'] = '交易不存在';
$error['trade.create_failed'] = '创建交易失败';
$error['trade.invalid_channel'] = '无效的平台类型';
$error['trade.invalid_status'] = '无效的状态类型';
$error['trade.close_not_allowed'] = '当前不允许关闭交易';
$error['trade.refund_not_allowed'] = '当前不允许交易退款';
$error['trade.refund_request_existed'] = '退款申请已经存在，请等待处理结果';

/**
 * 退款相关
 */
$error['refund.not_found'] = '退款不存在';
$error['refund.apply_note_too_short'] = '退款原因太短（少于2个字符）';
$error['refund.apply_note_too_long'] = '退款原因太长（多于255个字符）';
$error['refund.review_note_too_short'] = '审核备注太短（少于2个字符）';
$error['refund.review_note_too_long'] = '审核备注太长（多于255个字符）';
$error['refund.cancel_not_allowed'] = '当前不允许取消退款';
$error['refund.review_not_allowed'] = '当前不允许审核退款';
$error['refund.invalid_amount'] = '无效的退款金额';
$error['refund.invalid_status'] = '无效的状态类型';

/**
 * 角色相关
 */
$error['role.not_found'] = '角色不存在';
$error['role.name_too_short'] = '名称太短（少于2个字符）';
$error['role.name_too_long'] = '名称太长（超过30个字符）';
$error['role.summary_too_long'] = '描述太长（超过255个字符）';
$error['role.route_required'] = '角色权限不能为空';

/**
 * 用户限额
 */
$error['user_limit.reach_favorite_limit'] = '超出收藏限额';
$error['user_limit.reach_daily_report_limit'] = '超出每日举报限额';
$error['user_limit.reach_daily_article_limit'] = '超出每日文章限额';
$error['user_limit.reach_daily_question_limit'] = '超出每日提问限额';
$error['user_limit.reach_daily_answer_limit'] = '超出每日回答限额';
$error['user_limit.reach_daily_comment_limit'] = '超出每日评论限额';
$error['user_limit.reach_daily_consult_limit'] = '超出每日咨询限额';
$error['user_limit.reach_daily_order_limit'] = '超出每日订单限额';
$error['user_limit.reach_daily_like_limit'] = '超出每日点赞限额';

/**
 * 文章查询
 */
$error['article_query.invalid_tag'] = '无效的标签类别';
$error['article_query.invalid_sort'] = '无效的排序类别';

/**
 * 课程查询
 */
$error['course_query.invalid_model'] = '无效的模型类别';
$error['course_query.invalid_level'] = '无效的难度类别';
$error['course_query.invalid_sort'] = '无效的排序类别';

/**
 * 课时学习
 */
$error['learning.invalid_request_id'] = '无效的请求编号';
$error['learning.invalid_plan_id'] = '无效的计划编号';
$error['learning.invalid_interval_time'] = '无效的间隔时间';
$error['learning.invalid_position'] = '无效的播放位置';

/**
 * 试题相关
 */
$error['exam_question.not_found'] = '试题不存在';
$error['exam_question.invalid_parent'] = '无效的分类';
$error['exam_question.invalid_category'] = '无效的父题';
$error['exam_question.invalid_model'] = '无效的题型类别';
$error['exam_question.invalid_level'] = '无效的难度级别';
$error['exam_question.invalid_answer'] = '无效的答案';
$error['exam_question.invalid_score'] = '无效的分数（范围：1-30）';
$error['exam_question.invalid_priority'] = '无效的排序值（范围：1-255）';
$error['exam_question.invalid_publish_status'] = '无效的发布状态';
$error['exam_question.invalid_featured_status'] = '无效的推荐状态';
$error['exam_question.topic_required'] = '请填写题干';
$error['exam_question.answer_required'] = '请填写答案';
$error['exam_question.answer_choice_not_enough'] = '已填写答案选项不足（少于两个）';

/**
 * 试卷相关
 */
$error['exam_paper.not_found'] = '试题不存在';
$error['exam_paper.invalid_pack_type'] = '无效的组卷方式';
$error['exam_paper.invalid_grade_type'] = '无效的阅卷方式';
$error['exam_paper.invalid_category'] = '无效的分类';
$error['exam_paper.invalid_title'] = '无效的标题（范围：5-50个字符）';
$error['exam_paper.invalid_cover'] = '无效的封面';
$error['exam_paper.invalid_level'] = '无效的难度级别';
$error['exam_paper.invalid_duration'] = '无效的考试时长（范围：10-120分钟）';
$error['exam_paper.invalid_pass_score'] = '无效的及格分数（范围：1-100）';
$error['exam_paper.invalid_join_count'] = '无效的订阅数量（范围：0-999999）';
$error['exam_paper.invalid_market_price'] = '无效的市场价格（范围：1-999999）';
$error['exam_paper.invalid_vip_price'] = '无效的会员价格（范围：1-999999）';
$error['exam_paper.invalid_study_expiry'] = '无效的学习期限';
$error['exam_paper.invalid_refund_expiry'] = '无效的退款期限';
$error['exam_paper.invalid_publish_status'] = '无效的发布状态';
$error['exam_paper.invalid_featured_status'] = '无效的推荐状态';
$error['exam_paper.summary_too_long'] = '标题太长（多于255个字符）';
$error['exam_paper.keyword_too_long'] = '关键字太长（多于100个字符）';
$error['exam_paper.detail_too_long'] = '详情太长（多于5000个字符）';
$error['exam_paper.no_question_assigned'] = '尚未发现试题，请先选题组卷';

/**
 * 组卷相关
 */
$error['exam_paper_question.no_rand_conditions'] = '组卷条件不能为空';
$error['exam_paper_question.not_meet_all_conditions'] = '组卷条件不能满足';

/**
 * 考试资格
 */
$error['exam_paper_user.not_found'] = '考试资格不存在';
$error['exam_paper_user.invalid_auth_code'] = '无效的身份或过期';
$error['exam_paper_user.invalid_expiry_time'] = '无效的过期时间';
$error['exam_paper_user.exam_not_started'] = '考试尚未开始，当前无法操作';
$error['exam_paper_user.exam_over'] = '考试已结束，当前无法操作';
$error['exam_paper_user.paper_submitted'] = '试卷已提交，当前无法操作';
$error['exam_paper_user.grade_not_allowed'] = '阅卷模式有误，当前不允许人工评分';

/**
 * 答题记录
 */
$error['exam_question_user.not_found'] = '答题记录不存在';
$error['exam_question_user.too_many_answer_file'] = '超过4个答案附件';
$error['exam_question_user.invalid_answer_file'] = '无效的答案附件';
$error['exam_question_user.invalid_user_score'] = '无效的评分';
$error['exam_question_user.mark_not_allowed'] = '当前不允许评分';

/**
 * 考试查询
 */
$error['exam_paper_query.invalid_exam_type'] = '无效的测评类型';
$error['exam_paper_query.invalid_pack_type'] = '无效的组卷类型';
$error['exam_paper_query.invalid_level'] = '无效的难度类别';
$error['exam_paper_query.invalid_sort'] = '无效的排序类别';

/**
 * 联系信息相关
 */
$error['user_contact.not_found'] = '收货地址不存在';
$error['user_contact.invalid_name'] = '无效的用户姓名';
$error['user_contact.invalid_phone'] = '无效的手机号码';
$error['user_contact.invalid_add_province'] = '无效的地址（省）';
$error['user_contact.invalid_add_city'] = '无效的地址（市）';
$error['user_contact.invalid_add_county'] = '无效的地址（区）';
$error['user_contact.invalid_add_other'] = '无效的地址（详）';
$error['user_contact.reach_contact_limit'] = '超出收货地址限制数（最多10个）';

/**
 * 积分兑换相关
 */
$error['point_gift.not_found'] = '礼品不存在';
$error['point_gift.name_too_short'] = '礼品名称太短（少于2字符）';
$error['point_gift.name_too_long'] = '礼品名称太长（超过30字符）';
$error['point_gift.details_too_long'] = '礼品详情太长（多于10000个字符）';
$error['point_gift.invalid_cover'] = '无效的封面';
$error['point_gift.invalid_type'] = '无效的类型';
$error['point_gift.invalid_point'] = '无效的积分值（范围：1-999999）';
$error['point_gift.invalid_stock'] = '无效的库存值（范围：1-999999）';
$error['point_gift.invalid_redeem_limit'] = '无效的兑换限额（范围：1-10）';
$error['point_gift.invalid_publish_status'] = '无效的发布状态';

$error['point_gift_redeem.not_found'] = '兑换不存在';
$error['point_gift_redeem.course_not_published'] = '课程尚未发布';
$error['point_gift_redeem.course_free'] = '课程当前免费，无需积分兑换';
$error['point_gift_redeem.course_owned'] = '您已经拥有课程，无需积分兑换';
$error['point_gift_redeem.exam_paper_not_published'] = '试卷尚未发布';
$error['point_gift_redeem.exam_paper_free'] = '试卷当前免费，无需积分兑换';
$error['point_gift_redeem.exam_paper_owned'] = '您已经拥有试卷，无需积分兑换';
$error['point_gift_redeem.no_user_contact'] = '您尚未设置收货地址，请前往用户中心设置';
$error['point_gift_redeem.reach_redeem_limit'] = '超出物品兑换限额';
$error['point_gift_redeem.no_enough_point'] = '您的积分余额不足以抵扣此次兑换';
$error['point_gift_redeem.no_enough_stock'] = '兑换物品库存不足';

/**
 * 限时秒杀相关
 */
$error['flash_sale.not_found'] = '秒杀活动不存在';
$error['flash_sale.invalid_item_type'] = '无效的商品类型';
$error['flash_sale.invalid_start_time'] = '无效的开始时间';
$error['flash_sale.invalid_end_time'] = '无效的结束时间';
$error['flash_sale.invalid_time_range'] = '无效的起始区间';
$error['flash_sale.invalid_schedules'] = '无效的秒杀场次';
$error['flash_sale.invalid_price'] = '无效的价格（范围：1-999999）';
$error['flash_sale.invalid_stock'] = '无效的库存值（范围：1-999999）';
$error['flash_sale.invalid_publish_status'] = '无效的发布状态';
$error['flash_sale.active_item_existed'] = '该商品在当前时段已经存在秒杀活动';
$error['flash_sale.expired'] = '活动已过期';
$error['flash_sale.out_schedules'] = '当前时间和秒杀场次不匹配';
$error['flash_sale.not_paid'] = '订单尚未完成，请前往用户中心支付';
$error['flash_sale.out_stock'] = '下手太慢，商品被秒光啦';

/**
 * 兑换码相关
 */
$error['digital_card.not_found'] = '兑换码不存在';
$error['digital_card.remark_too_long'] = '备注信息太长（超过200字符）';
$error['digital_card.invalid_item_type'] = '无效的商品类型';
$error['digital_card.invalid_expiry'] = '无效的过期时间';
$error['digital_card.invalid_insert_count'] = '无效的生成数量（范围：1-100）';
$error['digital_card.has_expired'] = '兑换码已过期';
$error['digital_card.has_redeemed'] = '兑换码已消费';

/**
 * 优惠码相关
 */
$error['coupon.not_found'] = '优惠券不存在';
$error['coupon.not_active'] = '优惠券不可用';
$error['coupon.invalid_name'] = '无效的名称（2-30字符）';
$error['coupon.invalid_type'] = '无效的优惠类型';
$error['coupon.invalid_item_type'] = '无效的商品类型';
$error['coupon.invalid_private_status'] = '无效的私有状态';
$error['coupon.invalid_publish_status'] = '无效的发布状态';
$error['coupon.invalid_start_time'] = '无效的开始时间';
$error['coupon.invalid_end_time'] = '无效的结束时间';
$error['coupon.invalid_time_range'] = '无效的起始区间';
$error['coupon.invalid_consume_limit'] = '无效的门槛限额';
$error['coupon.invalid_total_usage'] = '无效的发行数量';
$error['coupon.invalid_user_usage'] = '无效的用户限额';
$error['coupon.invalid_deduct_amount'] = '无效的抵扣额度';
$error['coupon.invalid_discount_rate'] = '无效的抵扣比例';
$error['coupon.invalid_max_deduct_amount'] = '无效的最大抵扣额度（不小于1元）';

/**
 * 优惠券持有相关
 */
$error['coupon_user.not_found'] = '优惠券持有记录不存在';
$error['coupon_user.has_claimed'] = '优惠券已领取';
$error['coupon_user.reach_user_usage_limit'] = '超出优惠券用户使用次数上限';
$error['coupon_user.reach_total_usage_limit'] = '超出优惠券发行使用次数上限';

/**
 * 拼团相关
 */
$error['groupon.not_found'] = '拼团不存在';
$error['groupon.invalid_item_type'] = '无效的商品类型';
$error['groupon.invalid_publish_status'] = '无效的发布状态';
$error['groupon.invalid_start_time'] = '无效的开始时间';
$error['groupon.invalid_end_time'] = '无效的结束时间';
$error['groupon.invalid_time_range'] = '无效的起始区间';
$error['groupon.invalid_partner_limit'] = '无效的成团人数（范围：2-100人）';
$error['groupon.invalid_partner_expiry'] = '无效的成团期限（范围：1-7天）';
$error['groupon.invalid_virtual_partner'] = '无效的虚拟成团参数';
$error['groupon.invalid_member_price'] = '无效的团员价格';
$error['groupon.invalid_leader_price'] = '无效的团长价格';
$error['groupon.active_item_existed'] = '该商品在当前时段已经存在拼团活动';
$error['groupon.has_published_team'] = '你已经发起过拼团活动啦';
$error['groupon.has_joined_team'] = '你已经加入过拼团队伍啦';
$error['groupon.has_expired'] = '拼团活动已过期';

/**
 * 分销相关
 */
$error['distribution.not_found'] = '分销不存在';
$error['distribution.item_required'] = '请选择至少一个商品';
$error['distribution.invalid_item_type'] = '无效的商品类型';
$error['distribution.invalid_publish_status'] = '无效的发布状态';
$error['distribution.invalid_start_time'] = '无效的开始时间';
$error['distribution.invalid_end_time'] = '无效的结束时间';
$error['distribution.invalid_time_range'] = '无效的起始区间';
$error['distribution.invalid_v1_com_rate'] = '无效的一级佣金比例（范围：1-30%）';
$error['distribution.invalid_v2_com_rate'] = '无效的二级佣金比例（范围：1-20%）';
$error['distribution.invalid_v3_com_rate'] = '无效的三级佣金比例（范围：1-10%）';

/**
 * 提现相关
 */
$error['withdraw.not_found'] = '提现记录不存在';
$error['withdraw.invalid_status'] = '无效的状态类型';
$error['withdraw.invalid_amount'] = '无效的提现金额';
$error['withdraw.invalid_amount_range'] = '无效的提现金额范围';
$error['withdraw.review_note_too_short'] = '审核备注太短（少于2个字符）';
$error['withdraw.review_note_too_long'] = '审核备注太长（多于255个字符）';
$error['withdraw.channel_disabled'] = '提现平台已关闭';
$error['withdraw.has_applied'] = '提现处理中，请耐心等待';
$error['withdraw.reach_monthly_limit'] = '提现次数已用完';
$error['withdraw.balance_not_enough'] = '账户余额不足';
$error['withdraw.cancel_not_allowed'] = '当前不允许取消提现';
$error['withdraw.review_not_allowed'] = '当前不允许审核提现';

/**
 * 提现账户相关
 */
$error['withdraw_account.not_found'] = '提现记录不存在';
$error['withdraw_account.invalid_name'] = '无效的真实姓名';
$error['withdraw_account.invalid_channel'] = '无效的提现平台';
$error['withdraw_account.invalid_account'] = '无效的平台账户';

/**
 * 开票相关
 */
$error['invoice.not_found'] = '开票记录不存在';
$error['invoice.invalid_media_type'] = '无效的介质类型';
$error['invoice.invalid_full_no'] = '无效的发票号码';
$error['invoice.invalid_sort_no'] = '无效的发票代码';
$error['invoice.invalid_serial_no'] = '无效的发票编号';
$error['invoice.invalid_post_email'] = '无效的投递邮箱';
$error['invoice.invalid_voucher'] = '无效的发票凭证';
$error['invoice.invalid_status'] = '无效的状态类型';
$error['invoice.invalid_amount'] = '无效的开票金额';
$error['invoice.invalid_amount_range'] = '无效的开票金额范围';
$error['invoice.review_note_too_short'] = '审核备注太短（少于2个字符）';
$error['invoice.review_note_too_long'] = '审核备注太长（多于255个字符）';
$error['invoice.reach_monthly_limit'] = '提现次数已用完';
$error['invoice.balance_not_enough'] = '开票余额不足';
$error['invoice.cancel_not_allowed'] = '当前不允许取消开票';
$error['invoice.review_not_allowed'] = '当前不允许审核开票';

/**
 * 开票账户相关
 */
$error['invoice_account.not_found'] = '开票抬头不存在';
$error['invoice_account.invalid_usage_type'] = '无效的发票类型';
$error['invoice_account.invalid_head_type'] = '无效的抬头类型';
$error['invoice_account.invalid_head_name'] = '无效的抬头名称';
$error['invoice_account.invalid_tax_account'] = '无效的纳税人识别号';
$error['invoice_account.invalid_bank_name'] = '无效的开户行名称';
$error['invoice_account.invalid_bank_account'] = '无效的开户行账号';
$error['invoice_account.invalid_company_address'] = '无效的企业注册地址';
$error['invoice_account.invalid_company_phone'] = '无效的企业注册电话';
$error['invoice_account.usage_type_disabled'] = '所选发票类型不可用';
$error['invoice_account.reach_account_limit'] = '超出开票抬头限制数（最多10个）';

/**
 * 举报相关
 */
$error['report.not_found'] = '举报不存在';
$error['report.has_reported'] = '你已经举报过啦';
$error['report.reason_required'] = '请选择举报理由';
$error['report.remark_required'] = '请填写补充说明';

/**
 * 分组相关
 */
$error['group.not_found'] = '分组不存在';
$error['group.name_too_short'] = '名称太短（少于2字符）';
$error['group.name_too_long'] = '名称太长（多于30字符）';
$error['group.name_existed'] = '名称已经存在';
$error['group.invalid_expiry_time'] = '无效的过期时间';
$error['group.invalid_publish_status'] = '无效的发布状态';

$error['group_user.not_found'] = '分组学员关系不存在';
$error['group_course.not_found'] = '分组课程关系不存在';
$error['group_article.not_found'] = '分组专栏关系不存在';
$error['group_exam_paper.not_found'] = '分组试卷关系不存在';

return $error;
