<?php


namespace App\Http\Helpers\Utilities;

class ErrorLogging
{
    public static function getErrorLogUrl()
    {
        switch (env('APP_ENV')) {

            case 'local':
                $webhookUrl = env('LOG_SLACK_DEV_WEBHOOK_URL', 'https://hooks.slack.com/services/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');
                break;

            case 'staging':
                $webhookUrl = env('LOG_SLACK_STAGING_WEBHOOK_URL', 'https://hooks.slack.com/services/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');
                break;

            default:
                $webhookUrl = env('LOG_SLACK_WEBHOOK_URL', 'https://hooks.slack.com/services/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');
                break;

        }
        return $webhookUrl;
    }

}
