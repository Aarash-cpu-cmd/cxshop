<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'my_vendor/vendor/autoload.php'; // 更新路径

// 邮件模板数据
$templates = [
            'xmjg' => [
                        'title' => '订单异常警告',
                        'content' => '
                            <div style="font-family: Arial, sans-serif; color: #333; line-height: 1.5;">
                                <h2 style="color: #d9534f;">注意⚠️ 以下订单可能存在异常！</h2>
                                <p>用户您好，以下订单可能存在异常，请尽快处理：</p>
                                <ul style="color: #333;">
                                    【ordersList】
                                </ul>
                                <hr style="border: 1px solid #ddd; margin: 20px 0;">
                                <p style="font-size: 16px; color: #d9534f;">请尽快处理相关订单！</p>
                                <footer style="font-size: 12px; color: #aaa; margin-top: 20px;">
                                    <p>系统自动邮件，请勿回复。</p>
                                </footer>
                            </div>'
                    ],
            'ticketReply' => [
                        'title' => '工单回复通知',
                        'content' => '
                            <div style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; padding: 20px;">
                                <h2 style="color: #4CAF50;">工单回复通知</h2>
                                
                                <p>尊敬的用户，您好：</p>
                                <p>您提交的工单已收到，我们会尽快为您处理。以下是工单详情：</p>
                
                                <h3>工单标题：<span style="color: #007BFF;">【ticketTitle】</span></h3>
                                
                                <h4>商品信息</h4>
                                <ul style="padding-left: 20px;">
                                    <li>商品名称：<strong>【productName】</strong></li>
                                    <li>商品编号：<strong>【productID】</strong></li>
                                    <li>购买日期：<strong>【purchaseDate】</strong></li>
                                </ul>
                
                                <h4>问题内容</h4>
                                <p style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">
                                    【issueContent】
                                </p>
                
                                <hr style="border: 1px solid #ddd; margin: 30px 0;">
                                
                                <p>我们将尽快处理您的问题，如果有任何进一步的信息需要补充，请回复此邮件。</p>
                                
                                <p>谢谢您的耐心等待！</p>
                
                                <footer style="font-size: 12px; color: #aaa; margin-top: 20px;">
                                    <p>此邮件为系统自动发送，请勿回复。</p>
                                </footer>
                            </div>'
                    ],
            'ticketProcessed' => [
                'title' => '工单处理通知',
                'content' => '
                    <div style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; padding: 20px;">
                        <h2 style="color: #4CAF50;">工单处理通知</h2>
                        <p>尊敬的用户，您好：</p>
                        <p>您提交的工单已处理完成，处理结果如下：</p>
                        <h4>处理结果</h4>
                        <p style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">
                            【processResult】
                        </p>
                        <hr style="border: 1px solid #ddd; margin: 30px 0;">
                        <p>如有任何问题，欢迎随时联系我们。</p>
                        <p>感谢您使用我们的服务！</p>
                        <footer style="font-size: 12px; color: #aaa; margin-top: 20px;">
                            <p>此邮件为系统自动发送，请勿回复。</p>
                        </footer>
                    </div>'
            ],
        ];
        
        
// 邮件配置函数
function getMailConfig() {
    global $conf;
    return [
        'is_enabled' => $conf['mailstatus'] === "1",
        'smtp_server' => $conf['smtp_server'],
        'smtp_port' => (int)$conf['smtp_port'],
        'smtp_username' => $conf['mailusername'],
        'smtp_password' => $conf['mailpassword'],
        'smtp_encryption' => strtolower($conf['encryption']),
        'from_email' => $conf['from_email'],
        'from_name' => $conf['from_name'],
    ];
}

// 邮件发送函数（支持多个订单）
function sendEmail($toEmail, $toName, $templateId, $orders) {
    $mailConfig = getMailConfig();  // 获取邮件配置
    $errorLog = [];  // 初始化错误日志记录
    global $templates;
    // 检查是否开启邮件功能
    if (!$mailConfig['is_enabled']) {
        $errorLog[] = '邮件功能未开启';
        return $errorLog;
    }

    $mail = new PHPMailer(true);
    try {
        // 判断模板是否存在
        if (!isset($templates[$templateId])) {
            throw new Exception('邮件模板不存在');
        }

        // 构建订单信息列表
        $ordersList = '';
        foreach ($orders as $order) {
            $ordersList .= "<li>平台：<strong>{$order['ptname']}</strong>，账号：<strong>{$order['user']}</strong>，课程：<strong>{$order['kcname']}</strong>，备注：<strong>{$order['remarks']}</strong></li>";
        }

        // 记录构建的订单列表
        $errorLog[] = "构建的订单列表: $ordersList";

        // 服务器设置
        $mail->isSMTP();
        $mail->Host = $mailConfig['smtp_server'];
        $mail->SMTPAuth = true;
        $mail->Username = $mailConfig['smtp_username'];
        $mail->Password = $mailConfig['smtp_password'];
        $mail->SMTPSecure = $mailConfig['smtp_encryption'];
        $mail->Port = $mailConfig['smtp_port'];

        // 记录邮件服务器配置信息
        $errorLog[] = "邮件服务器设置: 主机 - {$mailConfig['smtp_server']}, 端口 - {$mailConfig['smtp_port']}";

        // 发件人设置
        $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
        $errorLog[] = "发件人: {$mailConfig['from_email']}";

        // 收件人设置
        $mail->addAddress($toEmail, $toName);
        $errorLog[] = "收件人: $toEmail";

        // 邮件内容设置
        $template = $templates[$templateId];
        $subject = $template['title'];
        $body = str_replace('【ordersList】', $ordersList, $template['content']); // 替换订单列表占位符

        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body); // 纯文本版本

        $errorLog[] = "邮件主题: $subject";
        $errorLog[] = "邮件内容: $body";

        // 发送邮件
        if ($mail->send()) {
            $errorLog[] = '邮件发送成功';
            return $errorLog;  // 邮件发送成功，返回日志
        } else {
            $errorLog[] = '邮件发送失败: ' . $mail->ErrorInfo;
            return $errorLog;  // 邮件发送失败，返回错误日志
        }
    } catch (Exception $e) {
        $errorLog[] = "邮件发送失败: {$mail->ErrorInfo}";
        $errorLog[] = "异常信息: {$e->getMessage()}";
        return $errorLog;  // 捕获异常并返回错误日志
    }
}

/*/示例：
调用 sendEmail 函数
$toEmail = '';
$toName = 'Recipient Name';
$templateId = 'xmjg'; // 使用异常提醒模板

// 模拟多个异常订单数据
$orders = [
    ['ptname' => '平台A', 'user' => '用户1', 'remarks' => '订单异常：考试未完成'],
    ['ptname' => '平台B', 'user' => '用户2', 'remarks' => '订单异常：时间耗尽'],
    ['ptname' => '平台C', 'user' => '用户3', 'remarks' => '订单异常：低于及格分数'],
];

$result = sendEmail($toEmail, $toName, $templateId, $orders);
echo $result === true ? '邮件发送成功' : $result;

/*/