###Api Doc

host: api.frezc.com

____

####MODEL
[error model]:
{
	code: 404,
    error: "error msg"
}

____
###API

[global]
request:
- app: 'todolite_android' (default)

#### general

#####/auth
[post]
Request:
- email
- password

Response:
{
	user: {
		id,
		nickname,
		email,
		created_at,
		/** only in todolite
		todo: number,
		layside: number,
		complete: number,
		abandon: number,
		*/
	},
	token
}

#####/user/{id}
[get]
request: {
	app: 'todolite_android'
}

response: user

#####/user
[post]
request: {
	token,
	app,
	nickname: 1 ~ 32
}

response: user (after updated)

#####/changePassword
[post]
request: {
	token,
	oldPassword,
	newPassword
}

response: string // if succeed, you should re-auth on all app

#####/resetPassword
[post]
request: {
	email,
	code,   // call /sendVerifyEmail to get code
	password
}

response: string // if succeed, you should re-auth on all app

#### http://statistics.frezc.com/anime-rank.html

#####/bgm_info/{id}
[get]
Request:
- id: bgm上相应的id

Response:
{}

#####/anime_rank
[get]
Request:
- time: 排行基本会是月更，该参数为年月的字符串，如201512。如果省略则返回最新的排行。(弃用)
- start: 从第几位开始，建议不要大于1161, 默认0
- limit: 返回的最大条数, 默认100
- lang: 标题显示的语言(cn, jp, en), 默认cn

Response:
{
	[{
		relate_id: relate表中的id，
    score: 分数，
    ann_score: ann的评分，0代表不和要求的分数，
    ann_score_rank: ann的分数排名，0代表无排名，
    ann_pop_rank: ann的热度排行，同上，
    ann_votes: ann的投票人数，同上
    bgm_...: 同上，
    sati_...: 同上，
    name: 番名
	}],
	update_date: 更新时间
}

#####/relate_info/{id}
[get]
{
		id
		name_jp
		name_en
		name_cn
		image: 番剧的图片，引用bgm的资源
		ann_url
		bgm_url
		sati_url
		air_date
		type: 番剧类型
		eps: 番剧集数，一般movie类型为0
}

#####/fetchAnimelist
[get]
response同http://api.bgm.tv/calendar

#### https://github.com/Frezc/TodoLite

#####/todos/{id}
[get]
request: {
	token: 'required'
}

response: (todo){
	id,
	user_id,
	title,
	status,
	type,
	start_at,
	urgent_at,
	deadline,
	priority,
	location,
	end_at,
	contents: [{
		content: text,
		status: string
	}]
	created_at,
	updated_at
}

#####/todos/{id}
[post]
request: {
	token,
	title,
	type,
	start_at,
	urgent_at,
	deadline,
	priority,
	location,
	contents: [{content, status}]
	updated_at: 用于延迟更新，如果该项早于数据库的updated_at则会返回错误
}
↑↑↑
update

#####/todos
[post]
↑↑↑
create

#####/todolist
[get]
request: {
	token,
	status: 'todo,complete,layside,abandon', // empty as all
	types: 'default,work' // list of types
	orderBy: 'updated_at', // updated_at as default, start_at, priority, end_at(while complete or abandon)
	direction: 'desc', // asc or desc
	offset: number, // 0 as default
	limit: number,   // 1000 as default
	keyword: string, // search in title and location
}

response: {
	all: number of whole items,
	todolist: [todo]
}

#####/history
[get]
request: {
	token,
	complete: 0 or 1,   // show complete, 1 as default
	abandon: 0 or 1,    // same as complete
	types: 'default,work' // list of types
	offset: number, // 0 as default
	limit: number,   // 50 as default
	keyword: string, // search in title and location
	year: number     // [2016, now]
}

response: {
	all: number of whole items,
	todolist: [todo]
}

#####/todos/{id}/finish
[post]
request: {
	token,
	status: 'complete' or 'abandon'
}

response: todo

#####/todos/{id}/layside
[post]
request: {
	token,
	status: 'todo' or 'layside'
}

response: todo

#### nimingban

#####/nimingban/id
[get]
request: {
	oldId    // can be null, server will check if this id is valid
}

response: {
	id
}

#####/nimingban/branches
[get]
request: {
	section: '游戏', // required
	offset: 0,
	limit: 10,
	withReplies: 3   // default
}

response: {
	all: 77,
	branches: [{
		...branch  // with 3 replies
	}]
}

#####/nimingban/branches
[post]
request: {
	section: '游戏',
	authorId: fd33sfe3e,
	authorName: '无名氏',
	title: '',
	content: ''
}

response: {
	...   // see /nimingban/branches/{id}
}

#####/nimingban/branches/{id}
[get]
request: {
	withReplies: 20
}

response: {
	id: 1,
	authorId: 'fd33sfe3e',
	authorName: '无名氏',   // [0, 16]
	created_at,
	updated_at,
	title: '',       // [0, 32]
	content: '',
	repliesNum: 10,  
	replies: [{
		id: 1,
		floor: 39,   // 楼层
		replyToId: 1,  // reply id
		replyToFloor: 1, 
		branchId: 1,
		authorId: ffe21da2,
		authorName: '无名氏',
		created_at,
		// updated_at,
		content: ''
	}]
}

#####/nimingban/branches/{id}/replies
[get]
request: {
	offset: 0,
	limit: 20
}

response: {
	all: 10,
	replies: [{
		... see above
	}]
}

#####/nimingban/branches/{id}/replies
[post]
request: {
	authorId: 'fd33sfe3e',
	authorName: '无名氏',
	content: '',
	replyToFloor: 2,   // 回复的楼数
}

response: {
	...see above
}
