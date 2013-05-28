require 'rubygems'
require 'koala'
require 'unicode'
require 'rmetaweblog'
require 'mysql'

# Command-line arguments
@username = ARGV[0];
 
# Application ID and SECRET to access facebook account
@api_key = "159335287416086";
@api_secret = "02c7d1369768a0a602cac78caebce7d3";

# Blog information for test purpose
@blog_hostname = "www.linus.pe.kr"
@blog_api_path = "/xmlrpc.php"
@blog_url = "http://www.linus.pe.kr/home/wordpress"
@blog_id = "hwijung"
@blog_password = "!qazxsw2"
@category = [ "Daily Life" ]
@time_locale = 9
@facebook_name = "hwijung.ryu"
 
@yesterday = Date.today - 1;

#initialize 
# @graph = Koala::Facebook::API.new("AAACEdEose0cBAKVZCYHkZB42VwI5JJDLfZAXpz0J6fka0Hto10R4zNlbEETd2Lxo5UvKk94EGnMedxh06tgZCo8v0B5YjTC2awNOj46fVAZDZD");
@db_con = Mysql.new 'localhost', 'root', '!qazxsw2'
rs = @db_con.query 'use f2w'
rs = @db_con.query 'SELECT * FROM fb_account'
puts rs.fetch_row

# Create Blog Object
# @blog = RMetaWebLog.new(@blog_hostname, @blog_api_path, {
#	:blog_url => @blog_url,
#	:blog_id => "1111",
#	:api_user => @blog_id,
#	:api_pass => @blog_password } )

# Text to HTML Link
# @generic_URL_regexp = Regexp.new( '(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)', Regexp::MULTILINE | Regexp::IGNORECASE )
# @starts_with_www_regexp = Regexp.new( '(^|[\n ])((www)\.[^ \"\t\n\r<]*)', Regexp::MULTILINE | Regexp::IGNORECASE )
# @starts_with_ftp_regexp = Regexp.new( '(^|[\n ])((ftp)\.[^ \"\t\n\r<]*)', Regexp::MULTILINE | Regexp::IGNORECASE )
# @email_regexp = Regexp.new( '(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)', Regexp::IGNORECASE )

=begin
def linkify( text )
  s = text.to_s
  s.gsub!( @generic_URL_regexp, '\1<a href="\2">\2</a>' )
  s.gsub!( @starts_with_www_regexp, '\1<a href="http://\2">\2</a>' )
  s.gsub!( @starts_with_ftp_regexp, '\1<a href="ftp://\2">\2</a>' )
  s.gsub!( @email_regexp, '\1<a href="mailto:\2@\3">\2@\3</a>' )
  s
end 

# Build Title
post_title = "#{@yesterday.year}년" + " #{@yesterday.month}월" + " #{@yesterday.day}일의 사건사고"

# Build Contents
@post_contents = "<UL style=\"PADDING-BOTTOM: 0px; LIST-STYLE-TYPE: none; PADDING-LEFT: 10px; WIDTH: 90%; PADDING-RIGHT: 10px; LIST-STYLE-IMAGE: none; PADDING-TOP: 0px\">"
@post_count = 0
facebook_entry_template = "<LI style=\"BORDER-BOTTOM: #ddd 1px dashed; PADDING-BOTTOM: 5px; LIST-STYLE-TYPE: none; PADDING-LEFT: 0px; PADDING-RIGHT: 0px; LIST-STYLE-IMAGE: none; PADDING-TOP: 7px\"> %s %s <A style=\"COLOR: #646464; FONT-SIZE: 8pt\" href=\"http://www.facebook.com/%s/posts/%s\" target=_blank>#</A></LI>"


# get facebook user information
@user_object = @graph.get_object(@facebook_name);

# get facebook user statuses
@user_statuses = @graph.get_connections(@facebook_name,"statuses");

#
@user_statuses.each do | status |
	@time = DateTime.parse( status["updated_time"] )
	
#	puts @time.year.to_s + "/" + @time.month.to_s + "/" + @time.day.to_s
#	puts @yesterday.year.to_s + "/" + @yesterday.month.to_s + "/" + @yesterday.day.to_s
	
	if @time.year == @yesterday.year && @time.month == @yesterday.month && @time.day == @yesterday.day
		@post_date_string = sprintf( "%d:%d", @time.hour.to_s, @time.min.to_s )
		@post_contents = sprintf( facebook_entry_template, linkify( status["message"] ), @post_date_string, @facebook_name, status["id"] ) + @post_contents 
		@post_count = @post_count + 1
	end
end

@post_contents = @post_contents +  "</UL><DIV style=\"TEXT-ALIGN: right; PADDING-BOTTOM: 0px; PADDING-LEFT: 0px; WIDTH: 95%; PADDING-RIGHT: 0px; PADDING-TOP: 5px\"><A style=\"FLOAT: right; COLOR: #595454; FONT-SIZE: 8pt; TEXT-DECORATION: none\" href=\"http://www.facebook.com/hwijung.ryu\" target=_blank>from facebook</A></DIV>"

# post
if @post_count != 0
	@blog.new_post( post_title, @post_contents, @category )
	puts "[" + DateTime.now.to_s + "] " + "facebook posts are successfully posted"
else
	puts "[" + DateTime.now.to_s + "] " + "There's no post to post"
end 

puts post_title  
puts @post_contents
=end