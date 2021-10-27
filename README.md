A CLI wrapper for the Zendesk Support API. Built with [Laravel Zero](https://laravel-zero.com/)

## Installation

Download the `zd` Phar file using `wget` or `curl`:

curl -O https://raw.githubusercontent.com/andrewfleming/zd-cli/main/builds/zd-cli.phar

Next, check the Phar file to verify that it’s working:

```bash
php zd-cli
```

To use ZD-CLI from the command line by typing `zd`, make the file executable and move it to somewhere in your `PATH`. For example:

```bash
chmod +x zd-cli.phar
sudo mv zd-cli.phar /usr/local/bin/zd
```


## Configuration

Before using ZD-CLI, and admin user will need to [register an OAuth client with Zendesk](https://support.zendesk.com/hc/en-us/articles/4408845965210#topic_s21_lfs_qk).
The `Redirect URLs` value for your OAuth application must include 

```
http://127.0.0.1:8090/authorization-code/callback
```

Before you authentiate, you'll need to provide the configuration values for connecting to your Zendesk Support account. You'll need your 
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

Clone this repo to your local machine. Change into the zd-cli directory and run composer install. Commands can then be 
run using the following pattern.

```
$ php zd-cli {COMMAND}
```
