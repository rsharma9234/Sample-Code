import auth from 'feathers-authentication'
import jwt from 'feathers-authentication-jwt'
import local from 'feathers-authentication-local'

export default function () {
  const app = this
  const config = app.get('auth')

  app.configure(auth(config))
    .configure(jwt())
    .configure(local())

  app.service('authentication').hooks({
    before: {
      create: [
        auth.hooks.authenticate([ 'local' ])
      ]
    }
  })
}
