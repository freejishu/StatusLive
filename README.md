# StatusLive &nbsp;<a href="https://github.com/freejishu/StatusLive/stargazers"><img src="https://img.shields.io/github/stars/freejishu/StatusLive?style=flat" alt="Stars"></a> <a href="https://github.com/freejishu/StatusLive/issues"><img alt="GitHub issues" src="https://img.shields.io/github/issues/freejishu/StatusLive"></a>

<p align="center"> 简洁 · 快速 · 轻便 </p>

![qhn.webp](https://s2.loli.net/2022/10/31/N4sCi7YDIZ8XxST.webp)

## What's StatusLive?

StatusLive 是一个基于 Uptimerobot 的状态页，数据基于 Uptimerobot API 而来，开箱即用。

StatusLive is a status page based on Uptimerobot. The data is based on Uptimerobot API.

注册一个 Uptimerobot 账户并添加监测点，即可搭建属于自己的、高可用的状态页面。

Register an Uptimerobot account and add monitoring points to build your own status page. 

## Demo

https://status.freejishu.com/

## How to use

- 最经典的打开方式 ( `dist` + `conf.json` + `core.php` ) ：

    1. 注册一个 `UptimeRobot` 账户并添加监控节点。

    2. ⭐Star 一下，然后从 [Releases][1] 下载最新版本并解压。

    3. 复制 `conf.example.conf` 到 `conf.json` ，并配置配置文件 [conf.json][2]
        ```
        {
            "config_title": "状态监控",      //页面标题
            "config_title_english": "StatusLive",    //页面副标题

            "config_mode": 2,  //模式选项，1为公开模式，2为隐私模式（模式区别请看下方）
            "config_readonly_apikey": "USE_SERVER_APIKEY", //公开模式用，填写从UptimeRobot后台获得的ReadOnly-ApiKey
            "config_proxy_link": "/core.php", //隐私模式用，反代访问路径

            "config_history_time": 60, //获取过去 X 天的可用率，单位为天
            "config_logs_history_days": 30, //获取过去 X 天的状态日志，单位为天

            "config_success_min": 98, //合格(success)等级标准，低于此数字为警告(warning)等级
            "config_warning_min": 90, //警告(warning)等级标准，低于此数字为危险(danger)等级
            "config_auto_refresh_seconds": 60, //自动刷新时间，单位为秒，填写0为禁用自动刷新

            "logs_each_page": 10  //日志模块每页展示行数，v2.1新增，作用于日志查看区
        }
        ```
    - **公开模式（不推荐）**

        最简单、快速的开箱方式。系统会根据 `conf.json` 内的 `config_readonly_apikey` 直接请求 UptimeRobot API 接口。此模式不需要 `core.php` 。
        
        `conf.json` 内应该如此填写：
        
        ```
        "config_mode": 1, 
        "config_readonly_apikey": "ur609xxx-27fxxxxxxxxxxxxxxxxxxxxx",
        ```

        **注意：此模式下一定要使用 `只读ApiKey (Read-Only API Key)`，非只读ApiKey的泄露会导致其他人使用官方 API 操纵账户！**
        
        如果觉得直接请求 UptimeRobot API 接口速度有些差，或不想暴露部分关键字段，您也可以使用下面的隐私模式反代以提高速度。

    - **隐私模式（推荐）**

        由于UptimeRobot API返回数据内包含 `url` 、 `http_username` 、 `http_password` 、 `port` 等字段，直接请求可能会导致真实域名、IP等泄露；同时对免费账户，UptimeRobot 的 API 存在 QPS 限制。故推荐使用隐私模式，可隐去关键字段并针对性缓存。

        `conf.json` 内应该如此填写：

        ```
        "config_mode": 2, 
        "config_readonly_apikey": "USE_SERVER_APIKEY", //无需再填写apikey，以core.php中为准
        "config_proxy_link": "/core.php",  //填写你的core.php路径
        ```

        本程序自带一个php的反代文件。对反代文件 `core.example.php` ，您需要先复制到 `core.php` （当然其他名字也可以，`config.json` 中的 `config_proxy_link` 字段需同步更新），再修改 `core.php` 的部分配置：
        
        ```
        //在这里填入你的API_KEY，如果使用公开模式则置空避免key被更改。
        $apikey = 'ur609264-xxxxxxxxxxxxxxxxxxxxxxxx';

        //json缓存文件名，可自行配置
        $file_name = 'uptime.json';

        //缓存时间，单位为秒，因为UptimeRobotAPI免费用户调用限制为10次/分钟故不建议低于6
        $cache_time = 10;
        ```
        
        接下来您可以将其放到任意服务器上，只需做好 CORS 和在 `config_proxy_link` 中填写好地址即可。
        
        关于反代机制，除了部署 `core.php` ，还有很多玩法，请参照下文中**更多打开方式**部分。

    - `conf.json` 的其余字段根据上文提示填写即可。注意JSON文件不能存在`注释`。

    4. 上传到服务器，然后 Enjoy it！

<br />

- 更多打开方式：
    
    1. `conf.json` 可以被环境变量替代。若您部署到类似 Cloudflare Pages 或 Vercel 此类平台时，可通过填写环境变量生成 `.env` 文件。当检测到环境变量存在时，无需再修改或部署 `conf.json`，程序将根据环境变量启动。详见：[如何部署 StatusLive 到静态资源平台？](https://github.com/freejishu/StatusLive/discussions/30)。

    2. 隐私模式需要用到的 `core.php` 可以有多种部署方式，如：
        - 可以使用 Cloudflare Worker 替代，详见：[使用 Cloudflare Workers 替代 Core.php 实现反代](https://github.com/freejishu/StatusLive/discussions/28)。
        - 如果计划将 `core.php` 用于如 `Vercel` 等 Serverless Functions 平台，请注释掉 `core.php` 的[第49行](https://github.com/freejishu/StatusLive/blob/67ebdce931332255f06dc0635aa0d88aa589999d/public/core.example.php#L49)避免写入错误。
        - 如果懒得架设 `core.php` ，可以使用由开发者提供的公共反代。请参照 [StatusLive公共反代使用说明](https://github.com/freejishu/StatusLive/discussions/15) 。
        - 关于 `core.php` 的更多细节，请参照 [常见问题汇总（v2.0）](https://github.com/freejishu/StatusLive/discussions/3)。


## Migration from v1.x

v1.x 使用参照：https://www.freejishu.com/statuslive-for-you/

基于各种各样的考虑，v2.x 采用了全新的技术栈，故 v1.x 不能直接迁移到 v2.x 。但是不用担心，基于开箱即用的特性，你会很快上手 v2.x 的。

注：v2.x 通过切换分支的形式实现过渡，即原 master 分支被重命名为 v1.x，而 v2.x 重命名为 master ，并切换了一次默认分支。如果之前 fork 过项目，可能需要重新 fork 或进行同步操作才能继续操作。

## Licenses

MIT

## How to Rebuild

1. Clone the code
2. Project setup

    ```
    yarn install
    ```

3. Compiles and hot-reloads for development & Compiles and minifies for production

    ```
    yarn serve
    yarn build
    ```

4. Customize configuration See [Configuration Reference](https://cli.vuejs.org/config/).


[1]: https://github.com/freejishu/StatusLive/releases/latest
[2]: https://github.com/freejishu/StatusLive/blob/master/public/conf.json
[3]: https://github.com/freejishu/StatusLive/discussions/3
