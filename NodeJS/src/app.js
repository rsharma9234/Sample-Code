import path from 'path'
import favicon from 'serve-favicon'
import compress from 'compression'
import cors from 'cors'
import feathers from 'feathers'
import configuration from 'feathers-configuration'
import hooks from 'feathers-hooks'
import rest from 'feathers-rest'
import bodyParser from 'body-parser'
import Umzug from 'umzug'

import middleware from './middleware'
import services from './services'

const app = feathers()

app.configure(configuration())

app.use(compress())
  .options('*', cors())
  .use(cors())
  .use(favicon(path.join(app.get('public'), 'favicon.ico')))
  .use('/', feathers.static(app.get('public')))
  .use(bodyParser.json())
  .use(bodyParser.urlencoded({ extended: true }))

  .configure(hooks())
  .configure(rest())
  .configure(services)
  .configure(middleware)

// set up migrations
const sequelize = app.get('sequelize')
export const migrations = new Umzug({
  storage: 'sequelize',
  storageOptions: {
    sequelize
  },
  migrations: {
    path: process.env.NODE_ENV === 'production' ? 'build/migrations' : 'src/migrations',
    params: [ sequelize, app ]
  }
})

export default app
