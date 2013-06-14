require 'rubygems'
require 'koala'
require 'unicode'
require 'rmetaweblog'
require 'mysql2'

# Command-line arguments
# [0]: User ID, [1]: Since, [2]: Before
@username = ARGV[0]
@argv_since = ARGV[1]
@argv_before = ARGV[2]
 
# Application ID and SECRET to access facebook account
@api_key = "159335287416086"
@api_secret = "02c7d1369768a0a602cac78caebce7d3"

# Blog information for test purpose
@blog_hostname = "www.linus.pe.kr"
@blog_apipath = "/home/wordpress/xmlrpc.php"
@blog_url = "http://www.linus.pe.kr/home/wordpress/"
@blog_id = "hwijung"
@blog_password = "q1234567"
@category = [ "Daily Life" ]
@time_locale = 9
@fb_user = "hwijung.ryu"
 
# Yesterday
@yesterday = Time.now - ( 60 * 60 * 24 ) 
@yesterday_begin = Time.local( @yesterday.year, @yesterday.month, @yesterday.day, 0, 0, 0 )
@yesterday_end = Time.local( @yesterday.year, @yesterday.month, @yesterday.day, 23, 59, 59 )

# Time range by argument
unless @argv_since.nil?
	@since = Time.local( @argv_since[0,4], @argv_since[4,2], @argv_since[6,2], @argv_since[8,2], @argv_since[10,2], @argv_since[12,2] )
else
	@since = @yesterday_begin
end	

unless @argv_before.nil?
	@before = Time.local( @argv_before[0,4], @argv_before[4,2], @argv_before[6,2], @argv_before[8,2], @argv_before[10,2], @argv_before[12,2] )
else
	@before = @yesterday_end
end

# Create MySQL Database Connection
@db_con = Mysql2::Client.new( :host=>'localhost', :username=>'root', :password=>'!qazxsw2' )

# Use F2B database 
@db_con.query 'use f2b'

# Query Wordpress user information
results = @db_con.query( 'SELECT * FROM wp_account WHERE user = "' + @username + '"', :as=>'hash' )
results_template = $db_con.query( 'SELECT * FROM wp_template WHERE user = "' + @username + '"', :as=>'hash' )

# Set up blog account
results.each(:as => :hash) do |row| 
	@blog_id = row['wp_id']
	@blog_password = row['wp_password']
	@blog_url = row['wp_address']
	@blog_hostname = row['wp_hostname']
	@blog_apipath = row['wp_apipath']
	@blog_category = row['wp_category']
end

# get template
results_template.each(:as => :hash) do |row|
	@template_title = row['wp_title']
	@template_header = row['wp_header']
	@template_entry = row['wp_entry']
	@template_footer = row['wp_footer']
end


# Create Blog Object
@blog = RMetaWebLog.new(@blog_hostname, @blog_apipath, {
	:blog_url => @blog_url,
	:blog_id => "1111",
	:api_user => @blog_id,
 	:api_pass => @blog_password } )

# Query facebook user information
results = @db_con.query( 'SELECT * FROM fb_account WHERE user = "' + @username + '"' )

# Set up facebook account
results.each(:as => :hash) do |row|
	@fb_access_token = row['fb_access_token']
	@fb_user = row['fb_user']
end

# FB initialize 
@graph = Koala::Facebook::API.new(@fb_access_token);

# Text to HTML Link
@generic_URL_regexp = Regexp.new( '(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)', Regexp::MULTILINE | Regexp::IGNORECASE )
@starts_with_www_regexp = Regexp.new( '(^|[\n ])((www)\.[^ \"\t\n\r<]*)', Regexp::MULTILINE | Regexp::IGNORECASE )
@starts_with_ftp_regexp = Regexp.new( '(^|[\n ])((ftp)\.[^ \"\t\n\r<]*)', Regexp::MULTILINE | Regexp::IGNORECASE )
@email_regexp = Regexp.new( '(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)', Regexp::IGNORECASE )


def linkify( text )
  s = text.to_s
  s.gsub!( @generic_URL_regexp, '\1<a href="\2">\2</a>' )
  s.gsub!( @starts_with_www_regexp, '\1<a href="http://\2">\2</a>' )
  s.gsub!( @starts_with_ftp_regexp, '\1<a href="ftp://\2">\2</a>' )
  s.gsub!( @email_regexp, '\1<a href="mailto:\2@\3">\2@\3</a>' )
  s
end 

# Build Title
post_title = "#{@yesterday_begin.year}년" + " #{@yesterday_begin.month}월" + " #{@yesterday_begin.day}일의 사건사고"

# Build Contents
@post_contents = "<UL style=\"PADDING-BOTTOM: 0px; LIST-STYLE-TYPE: none; PADDING-LEFT: 10px; WIDTH: 90%; PADDING-RIGHT: 10px; LIST-STYLE-IMAGE: none; PADDING-TOP: 0px\">"
@post_count = 0
facebook_entry_template = "<LI style=\"BORDER-BOTTOM: #ddd 1px dashed; PADDING-BOTTOM: 5px; LIST-STYLE-TYPE: none; PADDING-LEFT: 0px; PADDING-RIGHT: 0px; LIST-STYLE-IMAGE: none; PADDING-TOP: 7px\"> %s %s <A style=\"COLOR: #646464; FONT-SIZE: 8pt\" href=\"http://www.facebook.com/%s/posts/%s\" target=_blank>#</A></LI>"

# get facebook user information
@user_object = @graph.get_object(@fb_user)

# get facebook user statuses
@user_statuses = @graph.get_connections(@fb_user,"statuses")

@user_statuses.each do | status |
	@time = DateTime.parse( status["updated_time"] )
	@time = @time.new_offset(9.0/24)
	
	@converted_time = Time.local( @time.year, @time.month, @time.day, @time.hour, @time.min, @time.sec )
	
	if (@since..@before).include?(@converted_time)
		@post_date_string = @converted_time.strftime( "at %l:%m%p" )
		@post_contents = sprintf( facebook_entry_template, linkify( status["message"] ), @post_date_string, @facebook_name, status["id"] ) + @post_contents 
		@post_count = @post_count + 1
	end
end

@post_contents = @post_contents +  "</UL><DIV style=\"TEXT-ALIGN: right; PADDING-BOTTOM: 0px; PADDING-LEFT: 0px; WIDTH: 95%; PADDING-RIGHT: 0px; PADDING-TOP: 5px\"><A style=\"FLOAT: right; COLOR: #595454; FONT-SIZE: 8pt; TEXT-DECORATION: none\" href=\"http://www.facebook.com/hwijung.ryu\" target=_blank>from facebook</A></DIV>"

# set category
unless @blog_category.nil?
	@category = @blog_category
end 

# post
if @post_count != 0
	# @blog.new_post( post_title, @post_contents, @category )
	puts "[" + DateTime.now.to_s + "] " +  "Total " + @post_count.to_s + " number of facebook posts are successfully posted."
else
	puts "[" + DateTime.now.to_s + "] " + "There's no post to post."
end 

