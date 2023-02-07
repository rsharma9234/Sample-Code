import auth from 'feathers-authentication'

export default {
  before: {
    all: [
      auth.hooks.authenticate('jwt')
    ]
  }
}
