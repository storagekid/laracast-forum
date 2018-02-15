let user = window.App.user;
module.exports = {
	owns(model, owner = 'user_id') {
		return model[owner] === user.id;
	},
	isAdmin() {
		return ['jgvillalba@dentix.es'].includes(user.email);
	}
};