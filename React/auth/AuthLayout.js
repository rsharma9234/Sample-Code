import React, { Component } from 'react'
import { Route } from 'react-router-dom'
import Paper from 'material-ui/Paper'

import LoginForm from './LoginForm'
import logo from '../images/logo_dark.png'

class AuthLayout extends Component {
  render () {
    return (
      <main className='auth page'>
        <Paper className='auth-form container' zDepth={1}>
          <div className='logo-container'>
            <img src={logo} alt='justtime' />
          </div>
          <Route path='/login' component={LoginForm} />
        </Paper>
      </main>
    )
  }
}

export default AuthLayout
