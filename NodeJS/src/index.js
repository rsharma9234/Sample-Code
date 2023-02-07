// always use UTC timezone
process.env.TZ = 'UTC'

import app from './app'

const port = app.get('port')

const server = app.listen(port)
server.on('listening', () => {
  console.log(`justtime-api application started on ${app.get('host')}:${port}`)
})
