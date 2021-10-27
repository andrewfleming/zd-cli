A CLI wrapper for the Zendesk Support API. Built with [Laravel Zero](https://laravel-zero.com/)

## Installation

Before using ZD-CLI, and admin user will need to [register an OAuth client with Zendesk](https://support.zendesk.com/hc/en-us/articles/4408845965210#topic_s21_lfs_qk).
The `Redirect URLs` value for your OAuth application must include 

```
http://127.0.0.1:8090/authorization-code/callback`
```

**Configuration**—provide the configuration values for connecting to your Zendesk Support account. You'll need your 
`Zendesk subdomain`, `Zendesk OAuth client ID`, and `Zendesk OAuth client secret`. Run the following and
provide the requested information

```
$ zd config
```

Once you've successfully saved your config options, you can authenticate with the following command. Visit the URL presented
to you and grant ZD-CLI access to your Zendesk account

```
$ zd auth
```

## Updating

```
zd self-update
```

## Development
