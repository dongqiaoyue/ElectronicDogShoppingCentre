const Koa = require('koa')
const app = new Koa()
const path = require('path')
const static = require('koa-static')
const httpProxy = require('http-proxy-middleware');
const k2c = require('koa2-connect');


//设置静态资源的路径
const staticPath = '/'

app.use(async (ctx, next) => {
  if (ctx.url.startsWith('/api')) { //匹配有api字段的请求url     
    ctx.respond = false // 绕过koa内置对象response ，写入原始res对象，而不是koa处理过的response
    await k2c(httpProxy({
      target: 'http://test.cuittk.cn/',
      changeOrigin: true,
      secure: false,
      pathRewrite: {
        '^/api': ''
      }
    }))(ctx, next);
  }
  await next()
})
app.use(static(
  path.join(__dirname, staticPath)
))

app.listen(8888, () => {
  console.log('server is starting')
})
