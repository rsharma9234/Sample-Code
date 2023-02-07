import decode from 'jwt-decode'

import apollo from '../apollo'

const jwtKey = 'token'

export default class Auth {
  static isSignedIn () {
    return this.retrieveToken() ? true : false
  }

  static storeToken (token) {
    window.sessionStorage.setItem(jwtKey, token)
  }

  static retrieveToken () {
    return window.sessionStorage.getItem(jwtKey)
  }

  static signOut () {
    apollo.resetStore()
    window.sessionStorage.removeItem(jwtKey)
    window.location.assign('/login')
  }

  static verifyJwt (token) {
    if (typeof token !== 'string') {
      throw new Error('Token provided is missing or not a string')
    }

    try {
      const payload = decode(token)

      if (this.payloadIsValid(payload)) {
        return payload
      }

      throw new Error('Invalid Token: expired')
    } catch (error) {
      throw new Error('Cannot decode malformed token', error)
    }
  }

  static payloadIsValid (payload) {
    return payload && payload.exp * 1000 > new Date().getTime()
  }

  static getCurrentUserId () {
    const token = this.retrieveToken()
    const payload = this.verifyJwt(token)

    return payload.userId
  }
}
