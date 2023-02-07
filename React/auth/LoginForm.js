import React, { Component } from 'react'
import TextField from 'material-ui/TextField'
import EmailIcon from 'material-ui/svg-icons/communication/email'
import LockIcon from 'material-ui/svg-icons/action/lock'
import RaisedButton from 'material-ui/RaisedButton'

import { AUTH_URL } from '../constants'
import auth from './auth'

class LoginForm extends Component {
  constructor (props) {
    super(props);

    this.state = {
      submitting: false,
      loggedIn: false,
      email: '',
      password: '',
      errorText: ''
    }
  }

  handleEmailChange = (event) => {
    this.setState({ email: event.target.value, errorText: '' })
  }

  handlePasswordChange = (event) => {
    this.setState({ password: event.target.value, errorText: '' })
  }

  handleSubmit = (event) => {
    this.setState({ submitting: true })

    const { email, password } = this.state

    fetch(AUTH_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        email,
        password
      })
    })
    .then((response) => {
      // http status = 2xx ?
      if (response.ok) {
        return response.json();
      }

      return Promise.reject(response);
    })
    .then(({ accessToken }) => {
      auth.storeToken(accessToken)
      return this.setState({
        submitting: false,
        loggedIn: true
      })
    })
    .catch((error) => {
      console.log(error);
      if (error.status >= 400) {
        this.setState({ errorText: 'ungültige E-Mail-Adresse und/oder Passwort' })
      }
      this.setState({ submitting: false, email: '', password: '' })
    })

    event.preventDefault()
  }

  render () {
    const { submitting, loggedIn, email, password, errorText } = this.state;
    const { push } = this.props

    if (loggedIn) {
      push('/');
    }

    return (
      <form className='login form' onSubmit={this.handleSubmit}>
        <div className='field'>
          <TextField
            fullWidth
            floatingLabelText='Email-Adresse'
            type='email'
            value={email}
            onChange={this.handleEmailChange}
            errorText={errorText}
          />
          <EmailIcon color='#be1f25' />
        </div>
        <div className='field'>
          <TextField
            fullWidth
            floatingLabelText='Passwort'
            type='password'
            value={password}
            onChange={this.handlePasswordChange}
            errorText={errorText}
          />
          <LockIcon color='#be1f25' />
        </div>
        <div style={{ marginTop: '2em' }}>
          <RaisedButton primary label='login' type='submit' disabled={submitting} />
        </div>
      </form>
    )
  }
}

export default LoginForm
