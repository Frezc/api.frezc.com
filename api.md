###Api Doc

host: api.frezc.com

____
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
}]

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
