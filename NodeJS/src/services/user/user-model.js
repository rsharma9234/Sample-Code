export default function (sequelize) {
  const { Sequelize, models } = sequelize

  return sequelize.define('user', {
    email: {
      type: Sequelize.STRING,
      allowNull: false,
      unique: true
    },
    password: {
      type: Sequelize.STRING,
      allowNull: false
    },
    deviceUserId: {
      type: Sequelize.INTEGER
    },
    roles: {
      type: Sequelize.ARRAY(Sequelize.STRING),
      defaultValue: []
    },
    firstname: {
      type: Sequelize.STRING
    },
    lastname: {
      type: Sequelize.STRING
    }
  }, {
    freezeTableName: true,
    getterMethods: {
      displayname () {
        let name = `${this.getDataValue('firstname') ? this.getDataValue('firstname') : ''} ${this.getDataValue('lastname') ? this.getDataValue('lastname') : ''}`

        name = name.trim()
        if (name === '') {
          name = this.getDataValue('email') || ''
        }

        return name
      },
      initials () {
        return this.displayname.split(' ').map((s) => s.charAt(0)).join('').toUpperCase()
      }
    },
    classMethods: {
      associate () {
        this.belongsToMany(models.timeTemplate, { through: models.userTimeTemplate })
      }
    }
  })
}
