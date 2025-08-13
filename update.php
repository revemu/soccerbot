<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Soccer Match</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css'>
<style>
.form-inline {
  margin-bottom: 2em;
}
</style>
<script>
  window.console = window.console || function(t) {};
</script>
<script>
  if (document.location.search.match(/type=embed/gi)) {
    window.parent.postMessage("resize", "*");
  }
</script>
</head>
<body translate="no">
<div id="todo-list-example">

<form id="myform" v-on:submit.prevent="addMatch()">
Match: {{ next_match.match_num }} <BR>
<input type="hidden" ref="match_num" :value="next_match.match_num">
WeekID: {{ next_match.week_id }} <BR>
<input type="hidden" ref="week_id" :value="next_match.week_id">
  
  <select ref="team_a_id" >
      <option v-for="(team, index) in teamcolors" :value="team.teamid"
              >{{ team.name }}
      </option>
  </select>
  
  <select ref="team_b_id" >
      <option v-for="(team, index) in teamcolors" :value="team.teamid"
              >{{ team.name }}
      </option>
  </select>
  <button>Add</button>
</form>

<table class="table">
      <tbody>
		<tr>
			<table v-for="(item, index) in all_match" :key="index" class="table">
				<tbody>
				<tr>
					
						<tr> 
							<td width="20"> {{ item.match_num }} </td>
							<td> {{ item.team_a_color }} </td>
							<td> {{ item.team_a_goal }} - {{ item.team_b_goal }} </td>
							<td> {{ item.team_b_color }} </td>
							<td><button>Edit</button></td>
						</tr>

				</tr>
				<tr height="50">
					<td>
					<li>Art</li>
					<li>Kyne</li>
					</td>
					<td>
					</td>
					<td>
					</td>
					<td>
					</td>
					<td>
					</td>
				</tr>
				</tbody>
			</table>
		</tr>
      </tbody>
</table>

</div>


<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js"></script>

<script id="rendered-js">


Vue.component('todo-item', {
  template: '\
    <li>\
      <input type="hidden" :value="title">\
	  {{ title }}\
      <button v-on:click="$emit(\'remove\')">Remove</button>\
    </li>\
  ',
  props: ['title']
})

new Vue({
  el: '#todo-list-example',
  data: {
    newTodoSelect: '',
	info: [],
	next_match: [],
	output: [],
	all_match: [],
	teamcolors: '',
    todos: [
      
    ]
	
  },
    mounted () {
		
    this.refreshMatch(0);
	
  },
  methods: {
    addNewTodo: function () {
      this.todos.push({
        id: this.newTodoSelect.id,
        title: this.newTodoSelect.name
      })
	  console.log(this.newTodoSelect.id + ' ' + this.newTodoSelect.name)
    },
	refreshMatch: function (weeknum) {
		axios
		.get('https://hook.revemu.org/get_info.php?cmd=get_last_matchweek&weeknum=' + weeknum)
		.then(response => {       
			this.info = response.data ;
			this.teamcolors = this.info.team_week ;
			this.next_match = this.info.next_match ;
			this.all_match = this.info.all_match ;
			console.log("refresh request")
		})

		.catch(e => {
			this.errors.push(e);
			console.log("Errors : " + e);
		});
    },
	addMatch () {
      //console.log(this.$refs.match_num.value) ;
	  //console.log(this.$refs.week_id.value) ;
	  //console.log(this.$refs.team_a_id.value) ;
	 //console.log(this.$refs.team_b_id.value) ;
	  
	  var formdata = new FormData();
		formdata.append('matchnum', this.$refs.match_num.value);
		formdata.append('weekid', this.$refs.week_id.value);
		formdata.append('teamaid', this.$refs.team_a_id.value);
		formdata.append('teambid', this.$refs.team_b_id.value);
		
		axios
		.post('https://hook.revemu.org/add_match.php', formdata)

		.then(response => {       
			this.output = response ;
		}) 
		//console.log("id = " + this.info.team_week.team24.id);
		this.refreshMatch(5) ;
	  
    }
	
	
  }
})
</script>
</body>
</html>