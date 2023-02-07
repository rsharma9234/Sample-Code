import React, { Component } from 'react'
import { Route, Redirect } from 'react-router-dom'

import auth from './auth'

class PrivateRoute extends Component {
  render () {
    const { component: Component, ...rest } = this.props

    return (
      <Route {...rest} render={(props) => (
        auth.isSignedIn()
        ? <Component {...props} />
        : <Redirect to='/login' />
      )} />
    )
  }
}

export default PrivateRoute
