export default {
	data() {
		return {
			errors: {}
		}
	},

	methods: {
		has(prop) 
		{
			return this.errors.hasOwnProperty(prop)
		},

		get(prop) 
		{
	        if (this.errors[prop]) 
	        {
	            return this.errors[prop][0];
	        }
	    },

		clear(field) 
		{
	        if (field) 
	        {
	            delete this.errors[field];

	            return;
	        }

	        this.errors = {};
	    }
	},

	computed:
	{
		valid()
		{
			return Object.keys(this.errors).length == 0
		}
	}
}